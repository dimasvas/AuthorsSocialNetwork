<?php
namespace AppBundle\Service\Serializer;

/**
 * Description of UpdateSerializer
 *
 * @author dimas
 */
class CompositionSubscribtionSerializer {
    
    public function collectionSerialize($collection){
        
        $result = [];
        
        /***
         * FIX PERFORMANCE, EVERY TIME IS CALLED COmposition. Query all Compositions form collection at once.
         */
        foreach($collection as $item){
            $result[] = [
                'id' => $item->getId(),
                'composition_id' => $item->getComposition()->getId(),
                'composition_title' => $item->getComposition()->getTitle(),
                'composition_img' => $item->getComposition()->getImageFile(),
                'created' => $item->getCreated()
            ];
        }
        
        return $result;
    }
}
