<?php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Composition;

/**
 * Description of CompositionUpdateEvent
 */
class CompositionUpdateEvent extends Event {
    
    protected $composition;
    
    protected $message;
    
    public function __construct(Composition $composition, $message)
    {
        $this->composition = $composition;
        $this->message = $message;
    }

    public function getComposition()
    {
        return $this->composition;
    }
    
    public function getMessage()
    {
        return $this->message;
    }
}
