<?php
namespace AppBundle\Service\RabbitMQ\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Message\Message;
use AppBundle\Entity\Message\MessageThread;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Description of SendMessageConsumer
 *
 * @author dimas
 * 
 * Check standalaone consumer command: app/console rabbitmq:consumer composition_send_message
 */
class SendMessageConsumer extends BaseAppConsumer 
{
    
    protected $debug = true;
    
    private $em;
    
    private $token;
    
    private $pusher;
    
    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage, $pusher) 
    {
        $this->em = $entityManager;
        $this->token = $tokenStorage->getToken();
        $this->pusher = $pusher;
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
        
        $this->flushLog(json_encode($message));
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
