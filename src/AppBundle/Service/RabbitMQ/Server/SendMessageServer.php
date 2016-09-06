<?php
namespace AppBundle\Service\RabbitMQ\Server;

use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageThread;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Service\RabbitMQ\Consumer\BaseAppConsumer;
use JMS\Serializer\SerializationContext;

/**
 * Description of SendMessageConsumer
 *
 * @author dimas
 * 
 */
class SendMessageServer extends BaseAppConsumer
{
    protected $debug = true;
    
    private $em;
    
    private $token;
    
    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, $serializer) 
    {
        $this->em = $entityManager;
        $this->token = $tokenStorage->getToken();
        $this->serializer = $serializer;
    }

    public function execute(AMQPMessage $msg)
    {   
        $this->flushLog($msg->body);
              
        $data = $this->jsonToObject($msg->body);
        
        $date = new \DateTime('now');
        
        $recipient = $this->getUser($data->recipient);
        $sender = $this->getUser($data->sender);
        
        $message = (new Message())->setBody($data->body)
                    ->setRecipient($recipient)
                    ->setSender($sender)
                    ->setCreated($date);
        
        $thread = $this->getThread($recipient, $sender, $date, $message);
        
        $message->setThread($thread);
        
        $this->em->persist($thread);
        $this->em->persist($message);
        $this->em->flush();
        
        return $this->serializer->serialize(
                $message, 
                'json', 
                SerializationContext::create()->setGroups(array('message'))
        );
    }
    
    private function getUser($id)
    {
        return $this->em->getRepository('UserBundle:User')->find($id);
    }
    
    private function getThread($recipient, $sender, $date, $message) 
    {
        $thread = $this->em->getRepository('AppBundle:Message\MessageThread')
                            ->getThread($recipient, $sender);
        
        if(!$thread){
            $thread = new MessageThread();
            $thread->addParticipants($sender, $recipient);
        }
        
        $thread->setLastMessage($message);
        $thread->setLastMessageCreated($date);
        
        return $thread;
    }
    
}
