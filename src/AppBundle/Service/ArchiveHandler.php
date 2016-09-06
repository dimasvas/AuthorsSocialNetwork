<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

/**
 * Description of ArchiveHandler
 *
 * @author dimas
 */
class ArchiveHandler {
    
    public function processContent ($entity)
    {
        $rc = new \ReflectionClass($entity);
       
        switch ($rc->getShortName()) {
            case 'Book':
                $entity->setContent('');
                break;
            default:
                throw new \Exception("Wrong Composition type {$rc->getShortName()}");
        }
    }
}
