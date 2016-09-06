<?php

namespace AppBundle\Service;


use AppBundle\Entity\CompositionEntityType;
use AppBundle\Form\CompositionTypes\BookType;
use AppBundle\Form\CompositionTypes\MusicType;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;

class CompositionFormTypeFactory
{
    public function getCompositionFromType(CompositionEntityType $type, $editMode = false){
        //TODO: Call Class by dynamic name, NO switch must be used
        switch ($type->getAlias()) {
            case "book":
                $entityType = new BookType($editMode);
                break;
            case "music":
                $entityType = new MusicType();
                break;
            default:
                throw new NoSuchOptionException("No type with alias name {$type->getAlias()}");
        }

        return $entityType;
    }
    
    public function create($class, $param = null) {
        
        if(!is_object($class)) {
            throw new Exception('Argument is not an object.');
        }
        
        $reflectionClass = new \ReflectionClass($class); 
       
        $formTypeClassName = 'AppBundle\Form\CompositionTypes' . '\\' . $reflectionClass->getShortName(). 'Type';
        
        if(!class_exists($formTypeClassName)) {
             throw new \Exception("Class  '{$formTypeClassName}' does not exists.");
        }
        
        return new $formTypeClassName($param);

    }
}