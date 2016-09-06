<?php

namespace AppBundle\Service;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrapView;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class Pager
 * @package AppBundle\Services
 */
class Pager
{
    private $router;
    
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    public function getResults(QueryBuilder $queryBuilder, $page = 1, $maxPerPage = 10)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        
        return (new Pagerfanta($adapter))
                ->setMaxPerPage($maxPerPage)
                ->setCurrentPage($page);
    }
    /**
     * Bulds Pager using Pagerfanta.
     *
     * @param QueryBuilder $queryBuilder
     * @param int          $page
     * @param int          $maxPerPage
     * @return array
     */
    public function getPagedResults(QueryBuilder $queryBuilder, $page = 1, $maxPerPage = 10)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($page);
        
        $pager->getNbPages();
        
        return [
            'entities' => $pager->getCurrentPageResults(),
            'pager' => $pager  
        ];
    }
    
    public function getArrayPagedResults(QueryBuilder $queryBuilder, $page = 1, $maxPerPage = 10)
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($page);
        
        return [
            'entities' => $pager->getCurrentPageResults(),
            'pager'  => [
                'hasPrevPage' => $pager->hasPreviousPage(),
                'hasNextPage' => $pager->hasNextPage(),
                'pagesNumber' => $pager->getNbPages(),
                'prevPage' => $pager->hasPreviousPage() ? $pager->getPreviousPage() : null,
                'nextPage' => $pager->hasNextPage() ? $pager->getNextPage(): null,
                'currentPage' => $pager->getCurrentPage(),
            ],    
        ];
    }        


    public function pagerToHtml(Pagerfanta $pager, $composition_id) 
    {   
        $routeGenerator = function($page) use($composition_id) {
            return   $this->router->getGenerator()->generate(
                    'comments_list', 
                    ['composition' => $composition_id, 'page' => $page]);
        };

        $view = new TwitterBootstrapView();
        
        $options = array(
                    'prev_message' => 'Пред',
                    'next_message' => 'След'
                    );
        
        $html = $view->render($pager, $routeGenerator, $options);
        
        return $html;
    }
    //TODO: Refactor pager service. A lot of pagers for numbers of actions!!!!!
    public function updatePagerToHtml(Pagerfanta $pager) 
    {
        if ($pager->getNbPages() == 1) {
            return '';
        }
        
        $routeGenerator = function($page) {
            return   $this->router->getGenerator()->generate(
                    'composition-update-list', 
                    ['page' => $page]);
        };

        $view = new TwitterBootstrapView();
        
        $options = array(
                    'prev_message' => 'Пред',
                    'next_message' => 'След'
                    );
        
        $html = $view->render($pager, $routeGenerator, $options);
        
        return $html;
    }
    
    public function subscribtionPagerToHtml(Pagerfanta $pager) 
    {
        if ($pager->getNbPages() == 1) {
            return '';
        }
        
        $routeGenerator = function($page) {
            return   $this->router->getGenerator()->generate(
                    'comp-subscriptions-list', 
                    ['page' => $page]);
        };

        $view = new TwitterBootstrapView();
        
        $options = array(
                    'prev_message' => 'Пред',
                    'next_message' => 'След'
                    );
        
        $html = $view->render($pager, $routeGenerator, $options);
        
        return $html;
    }
}