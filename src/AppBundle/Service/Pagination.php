<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use JMS\Serializer\Serializer;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use JMS\Serializer\SerializationContext;

class Pagination {
    
    const VISIBLE_PAGES = 6;
    
    private $serializer;
    
    private $router;
    
    public function __construct(Router $router, Serializer $serializer) 
    {
        $this->router = $router;
        
        $this->serializer = $serializer;
    }
    
    public function getORMResults(QueryBuilder $queryBuilder, $page = 1, $maxPerPage = 10)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        
        return (new Pagerfanta($adapter))
                ->setMaxPerPage($maxPerPage)
                ->setCurrentPage($page);
    }
    
    public function getArrayResults ($array, $page = 1, $maxPerPage = 10) 
    {
        $adapter = new ArrayAdapter($array);
        
        return (new Pagerfanta($adapter))
                ->setMaxPerPage($maxPerPage)
                ->setCurrentPage($page);
    }
    
    //getJsonPagedResults in future
    public function getArrayPagedResults(QueryBuilder $queryBuilder, $page = 1, $maxPerPage = 10) {
        
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($page);
        
        $results = $pager->getCurrentPageResults();
        
        $serialized = [];
        
        foreach ($results as $item) {
            $serialized[] = json_decode(
                    $this->serializer->serialize($item, 'json',  SerializationContext::create()->setGroups(array('list')))
                );
        }
        
        return [
            'collection' => $serialized,
            'pagination' => [
                'haveToPaginate' => $pager->haveToPaginate(),
                'visiblePages' => self::VISIBLE_PAGES,
                'totalPages' => $pager->getNbPages(),
                'hasPrevPage' => $pager->hasPreviousPage(),
                'hasNextPage' => $pager->hasNextPage()
            ]
        ];
    }
}
