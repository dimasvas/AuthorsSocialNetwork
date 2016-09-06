<?php

namespace AppBundle\Service\Serializer;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;

class UserSerializer {
    
    private $router;
    
    private $serializer;
    
    public function __construct(Router $router, Serializer $serializer)
    {
        $this->router = $router;
        $this->serializer = $serializer;
    }
    
    public function serializeCollection($collection)
    {
        $data = [];
        
        foreach($collection as $item) {
           $data[] = [
                'id' => $item->getId(),
                'username' => $item->getUsername(),
                'name' => $item->getName(),
                'surname' => $item->getSurname(),
                'isAuthor' => $item->getIsAuthor(),
                'locked' => $item->isLocked()
            ];
        }
        
        return $data;
    }
}
