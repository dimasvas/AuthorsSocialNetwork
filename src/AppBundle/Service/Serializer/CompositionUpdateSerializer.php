<?php
namespace AppBundle\Service\Serializer;

/**
 * Description of UpdateSerializer
 *
 * @author dimas
 */
class CompositionUpdateSerializer {
    
    public function collectionSerialize($collection){
        
        $result = [];
        
        /***
         * FIX PERFORMANCE, EVERY TIME IS CALLED COmposition. Query all Compositions form collection at once.
         */
        
        foreach($collection as $item){
            $result[] = [
                'id' => $item->getId(),
                'author_name' => $item->getAuthorName(),
                'composition_id' => $item->getCompositionId(),
                'composition_name' => $item->getCompositionTitle(),
                //'composition_img' => $item->getCompositionImageFile(),
                'message' => $item->getMessage(),
                'viewed' => $item->getViewed(),
                'created' => $item->getCreated()
            ];
        }
        
        return $result;
    }
}
