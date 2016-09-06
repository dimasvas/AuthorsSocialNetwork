<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\CompositionCategory;
use AppBundle\Entity\Genre;

/**
 * Category controller.
 *
 * @Route("/{_locale}/category", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 */
class CategoryController extends Controller
{
    /**
     * Lists all CompositionCategory entities.
     *
     * @Route("/", name="category_list")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $categories = $em->getRepository('AppBundle:CompositionCategory')
                ->getFullCategories();
        
        return $this->render(
            'AppBundle:Category:index.html.twig',
            [
                'categories' => $categories,
            ]    
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/sub/{id}/{page}", 
     *      name="category_show",
     *      defaults={"page" = 1}
     * )
     * @Method("GET")
     */
    public function showAction(CompositionCategory $category, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Composition')->getByCategory($category);
        
        $paginator = $this->get('app.pagination')->getORMResults($queryBuilder, $page, 20);

        return $this->render(
            'AppBundle:Category:show.html.twig',
            array(
                'section' => $category,
                'entities' => $paginator->getCurrentPageResults(),
                'pager' => $paginator,
                'title' => 'common.category',
                'type' => 'category'
            )
        );
    }
    
    /**
     * Finds and displays a Category entity.
     *
     * @Route("/genre/{id}/{page}", 
     *      name="show_by_genre",
     *      defaults={"page" = 1}
     * )
     * @Method("GET")
     */
    public function showByGenreAction(Genre $genre, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Composition')->getByGenre($genre);
        
        $paginator = $this->get('app.pagination')->getORMResults($queryBuilder, $page, 20);
        
        return $this->render(
            'AppBundle:Category:show.html.twig'  ,
            array(
                'section' => $genre,
                'entities' => $paginator->getCurrentPageResults(),
                'pager' => $paginator,
                'title' => 'common.genre',
                'type' => 'genre'
            )
        );
    }

}
