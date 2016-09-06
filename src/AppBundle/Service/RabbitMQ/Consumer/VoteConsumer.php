<?php
namespace AppBundle\Service\RabbitMQ\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Vote;

/**
 * Description of VoteConsumer
 *
 * @author dimas
 * 
 * Check standalaone consumer command: app/console rabbitmq:consumer composition_send_update
 */
class VoteConsumer extends BaseAppConsumer 
{
    
    protected $debug = true;
    
    private $em;
    
    public function __construct(EntityManager $entityManager) 
    {
        $this->em = $entityManager;
    }

    public function execute(AMQPMessage $msg)
    {   
        $this->flushLog($msg->body);
              
        $data = $this->jsonToObject($msg->body);
        
        $voterUser = $this->em->getRepository('UserBundle:User')->find($data->voterUser);
        $comment = $this->em->getRepository('AppBundle:Comment')->find($data->commentId);
        
        $this->setVote($voterUser, $comment, $data->type);
       
        $this->setFrienship($data->type, $comment);
    }
    
    private function setVote($voterUser, $comment, $type) 
    {
        $vote = new Vote();
        
        $vote->setUser($voterUser)
             ->setComment($comment)
             ->setType($type);   
            
        if($type == 'like') {
            $vote->setLike();
            $comment->increaseVote();
        } else {
            $vote->setDislike();
            $comment->decreaseVote();
        }
        
        $this->em->persist($vote);
        $this->em->persist($comment);
        $this->em->flush();
    }
    
    private function setFrienship($type, $comment) 
    {
        $commentOwner = $comment->getUser();
        
        $prevValue = $commentOwner->getFriendship();
        
        if($type == 'like'){
            $newValue = ($prevValue + 1);
        }else {
            $newValue = ($prevValue - 1);
        }
        
        $commentOwner->setFriendship($newValue);
         
        $this->em->persist($commentOwner);
        $this->em->flush();
    }
}
