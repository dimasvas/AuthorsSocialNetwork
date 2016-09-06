<?php
namespace AppBundle\Service\RabbitMQ\Producer;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
/**
 * Description of SendUpdateProducer
 *
 * @author dimas
 */
class VoteProducer extends Producer {
    function __construct(AMQPConnection $conn, AMQPChannel $ch = null, $consumerTag = null) {
        parent::__construct($conn, $ch, $consumerTag);
        
        $this->setContentType('application/json');
    }

    public function publish($msgBody, $routingKey = '', $additionalProperties = array()) {
        parent::publish(json_encode($msgBody), $routingKey, $additionalProperties);
    }
}
