<?php
namespace AppBundle\Service\RabbitMQ\Consumer;

use PhpAmqpLib\Message\AMQPMessage;
use Doctrine\ORM\EntityManager;

/**
 * Description of SendUpdateConsumer
 *
 * @author dimas
 * 
 * Check standalaone consumer command: app/console rabbitmq:consumer composition_send_update
 */
class SendUpdateConsumer extends BaseAppConsumer 
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
        
        $subscribtions = $this->getSubscribtions($data);
        
        $query = $this->prepareQuery($data, $subscribtions);
        
        $this->flushLog($query);
                
        $this->flushToDatabase($query);
        
    }
    
    private function getSubscribtions($msg)
    {
        return $this->em->getRepository('AppBundle:CompositionSubscription')
                ->findByComposition($msg->composition_id);
    }

    private function prepareQuery($msg, $subscribtions) 
    {
        $query = "INSERT INTO composition_update (user_id, author_name, composition_id, composition_title, message, viewed, created) VALUES ";
        
        foreach($subscribtions as $item){
            $query .= $this->prepareValues($msg, $item);
        }
        
        /**
         * TODO: Terrible. Rewrite logic in future.
         */
        return $this->fixQuery($query);
    }
    
    private function prepareValues($msg, $item)
    {
        $date = (new \DateTime('now'))->format('Y-m-d H:i:s');
        
        return "({$item->getUser()->getId()}, '{$msg->author_name}', {$msg->composition_id}, '{$msg->composition_title}', '{$msg->message}', false, '{$date}'),";
    }
    
    private function fixQuery($query){
        return substr($query, 0, -1);
    }
    
    private function flushToDatabase($query)
    {
        $connection = $this->em->getConnection();

        return $connection->prepare($query)->execute();
    }
}
