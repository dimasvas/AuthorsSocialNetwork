<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageThread;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use JMS\Serializer\SerializationContext;

/**
 * Message controller.
 *
 * @Route("/{_locale}/message", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 * @Security("has_role('ROLE_USER')");
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     * @Route("/list", name="message")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $threads = $em->getRepository('AppBundle:Message\MessageThread')
                        ->getArrayUserThreads($this->getUser(), true);
        
        $threadsMessage = $em->getRepository('AppBundle:Message\Message')
                            ->getMessages(array_column($threads, 'last_message_id'), $asArray = true);
        
        return $this->render(
            'AppBundle:Message:index.html.twig',
            array('threadsMessage' => $threadsMessage )    
        );
    }
    
    /**
     * Lists all Message entities.
     *
     * @Route("/threads/{page}", 
     *      name="thread_list", 
     *      defaults={"page" = 2}, 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("GET")
     */
    public function threadsAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Message\MessageThread')
                                    ->getArrayUserThreads($this->getUser());
        
        $paginator = $this->get('app.pagination')->getORMResults($queryBuilder, $page);

        $results = $paginator->getCurrentPageResults();
        //TODO: refactor
        $ids = [];
        foreach ($results as $item) {
            $ids[] = $item->getLastMessage()->getId();
        }
        
        $threadsMessage = $em->getRepository('AppBundle:Message\Message')
                            ->getMessages($ids);
        
        $messages = $this->get('jms_serializer')->serialize(
                        $threadsMessage, 'json', 
                        SerializationContext::create()->setGroups(array('message')));

        return new JsonResponse (
            array(
                'success' => true,
                'collection' => $messages,
                'pagination' => array(
                    'hasNextPage' => $paginator->hasNextPage(),
                    'nextPage' => $paginator->hasNextPage() ? $paginator->getNextPage() : null
                )
            )
        );
    }
    
     /**
     * Lists all Message entities.
     *
     * @Route("/list/{thread}/{page}", 
      *     name="thread_messages", 
      *     defaults={"page" = 1}, 
      *     condition="request.isXmlHttpRequest()",
      *     options={"expose"=true}
      * )
     * @Method("GET")
     */
    public function messageListAction(MessageThread $thread, $page)
    {        
        $this->denyAccessUnlessGranted('view',  $thread);
 
        $em = $this->getDoctrine()->getManager();
                
        $queryBuilder = $em->getRepository('AppBundle:Message\Message')
                                            ->getThreadMessages($thread);
        
        $paginator = $this->get('app.pagination')->getORMResults($queryBuilder, $page);
        
        $messages = $this->get('jms_serializer')->serialize(
                        $paginator->getCurrentPageResults()->getArrayCopy(), 
                        'json', 
                        SerializationContext::create()->setGroups(array('message')));
                
        return new JsonResponse (
            array(
                'success' => true,
                'recipient' => $this->getRecipient($thread),
                'collection' => $messages,
                'pagination' => array(
                    'hasNextPage' => $paginator->hasNextPage(), 
                    'nextPage' => $paginator->hasNextPage() ? $paginator->getNextPage() : null
                )
            )
        );
    }
    
    /**
     * Creates a new Message entity.
     *
     * @Route("/{recipient}", 
     *      name="message_create", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("POST")
     */
    public function createAction(Request $request, User $recipient)
    { 
        $date = new \DateTime('now');
        
        $message = (new Message())->setBody($request->request->get('body'))
                    ->setRecipient($recipient)
                    ->setSender($this->getUser())
                    ->setCreated(new \DateTime('now'));
        
        $this->denyAccessUnlessGranted('create', $message);
        
        $errors = $this->get('validator')->validate($message);
        
        if (count($errors) > 0) {
            return new Response(['success' => false,'message' => (string) $errors]);
        }
        
        //$response = $request->request->get('rpc') ?
          //          $this->sendToConsumer($request, $recipient) :
            //        $this->sendNonRpc($message, $recipient, $date);
        
        $response = $this->sendNonRpc($message, $recipient, $date);
        
        //$response = $this->sendClientRpc($request, $recipient);
        
        return new JsonResponse([
            'success' => true,
            'collection' => $response
        ]);
    }
    
    private function sendToConsumer ($request, $recipient) 
    {
        $this->get('old_sound_rabbit_mq.send_message_producer')
            ->publish([
                'body' => $request->request->get('body'),
                'recipient' => $recipient->getId(),
                'sender' =>$this->getUser()->getId()
        ]);
        
        return 'success';
    }
    
    private function sendNonRpc($message, $recipient, $date)
    {
        $thread = $this->getThread($recipient, $date, $message);
        $message->setThread($thread);
        
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($thread);
        $em->persist($message);
        $em->flush();
        
        $serialized = $this->get('jms_serializer')->serialize($message, 'json', 
                        SerializationContext::create()->setGroups(array('message')));
        
       // $pusher = $this->container->get('gos_web_socket.wamp.pusher');
        //push(data, route_name, route_arguments)
       // $pusher->push($serialized, 'message_topic', ['user' => $recipient->getId()]);

        return $serialized;
    }
    
    private function sendClientRpc($request, $recipient) 
    {
        $client = $this->get('old_sound_rabbit_mq.send_message_client_rpc');
        
        $correlationId = 'message_send_'. crc32(microtime());
        
        $data = json_encode(array(
            'body' => $request->request->get('body'),
            'recipient' => $recipient->getId(),
            'sender' =>$this->getUser()->getId()
        ));
        
        $client->addRequest($data, 'send_message_server', $correlationId , null, 60);
        
        try {
            $response = $client->getReplies();
        } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
            echo $e->getMessage();
        }
        
        return $response;
    }
    
    private function getRecipient(MessageThread $thread) 
    {
        $first = $thread->getFirstParticipant();
        $second = $thread->getSecondParticipant();

        return ($this->getUser()->isEqualTo($first) ? $second->getId() : $first->getId());
    }
    
    private function getThread($recipient, $date, $message) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $thread = $em->getRepository('AppBundle:Message\MessageThread')
                            ->getThread($recipient, $this->getUser());
        
        if(!$thread){
            $thread = new MessageThread();
            $thread->addParticipants($this->getUser(), $recipient);
        }
        
        $thread->setLastMessage($message);
        $thread->setLastMessageCreated($date);
        
        return $thread;
    }
}
