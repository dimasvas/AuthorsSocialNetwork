<?php

namespace AppBundle\Listener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Attachment;

/**
 * Description of UploadListener
 *
 * @author dimas
 */
class UploadListener {
    
    private $em;
    
    public function __construct(EntityManager $entityManager)
    {
       $this->em = $entityManager;
    }

    //TODO: MOVE TO CONTROLLER
    public function onUpload(PostPersistEvent $event)
    {
        $fileName = $event->getFile()->getFileName();
        $description = $event->getRequest()->request->get('description');
        $response = $event->getResponse();
        
        $attachment = new Attachment();
        
        $attachment->setComposition($response['composition'])
                    ->setDescription($description)
                    ->setFile($fileName)
                    ->setOwner($response['owner']);
        
        $this->em->persist($attachment);
        $this->em->flush();
        
        $response->offsetUnset('composition');
        $response->offsetUnset('owner');
        
        $response->offsetSet('description', $description ? $description : '');
        $response->offsetSet('file', $fileName);
        $response->offsetSet('id', $attachment->getId());
    }
}
