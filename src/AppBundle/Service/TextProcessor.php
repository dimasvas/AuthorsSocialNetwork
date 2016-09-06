<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Description of Texthandler
 *
 * @author dimas
 */
class TextProcessor 
{
    private $charPerPage = 3500;
    
    private $filter;
    
    public function __construct($filter)
    {
        $this->filter = $filter;
    }
    
    public function countPage($data) 
    {
        return count(explode("\n", wordwrap($data, $this->charPerPage)));
    }
    
    public function getPagedData($type, $page) 
    {
        if(!$type->getContent() and $page == 1) {
            return '';
        }
        
        $this->checkPages($type->getPages(), $page);
        
        $array_text = explode("\n", wordwrap($type->getContent(), $this->charPerPage));
        
        return $array_text[$page -1];
    }
    
    public function processText($data) 
    {
        $allowed_tags = array('p', 'i', 'b', 'em', 'strong', 'ul', 'ol', 'li');

        $this->filter->addAllowedTags($allowed_tags);
        
        return $this->filter->xss($data);
        
      //  $raw_text = $filter->xss($data);
                
//        // Split text 
//        $array_text = explode("\n", wordwrap($raw_text, 5300));
//        // Free memory
//        $raw_text = null;
//        //Count pages
//        $pages = count($array_text);
//        //Merge text
//        $processed = '';
//
//        foreach($array_text as $key => $value) {
//            $page = $key + 1;
//            $processed .= "<div class=page-{$page}>{$value}</div>";
//        }
//
//        //Free memory
//        $array_text = null;
//        //Save processed and pages
//        return $processed;
    }
    
    private function checkPages($totalPages, $currentPage)
    {
        if ($currentPage > $totalPages) {
            throw new NotFoundHttpException('Such page does not exists.');
        }
    }
}
