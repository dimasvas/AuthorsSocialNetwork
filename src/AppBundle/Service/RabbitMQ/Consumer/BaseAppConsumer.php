<?php
namespace AppBundle\Service\RabbitMQ\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Description of BaseConsumer
 *
 * @author dimas
 */
abstract class BaseAppConsumer implements ConsumerInterface {
    
    protected $debug = false;
    
    public function execute(AMQPMessage $msg){}
    
    protected function jsonToObject($msg){
        return json_decode($msg);
    }
    
    protected function flushLog($message){
        if($this->debug) {
            echo "{$message}".PHP_EOL;
        }
    } 
}
