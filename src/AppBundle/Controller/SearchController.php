<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\SearchType;
use AppBundle\Form\LanguageType;
use AppBundle\Form\CompositionStatusType;
use AppBundle\Entity\CompositionCategory;
use JMS\Serializer\SerializationContext;

/**
 * Description of SearchController
 *
 * @author dimas
 * 
 * @Route("/{_locale}/search", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 */
class SearchController extends Controller {
    
     /**
     * Search page
     *
     * @Route("/page/{page}", name="search_page", defaults={"page" = 1})
     * @Method("GET")
     */
    public function indexAction(Request $request, $page)
    {   
        
        return $this->render(
            'AppBundle:Search:index.html.twig',
            array(
                'category_form' => $this->createForm(SearchType::class)->createView(),
                'language_form' => $this->createForm(LanguageType::class)->createView(),
                'status_form' => $this->createForm(CompositionStatusType::class)->createView()
            )
        );
    }
    
    /**
     * Search page
     *
     * @Route("/results/{page}", 
     *      name="search_results", 
     *      defaults={"page" = 1},
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method("GET")
     */
    public function searchAction (Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Composition')
                    ->getBySearchParams($request->query->all());


        return new JsonResponse([
            'success' => true,
            'data' => $this->get('app.pagination')->getArrayPagedResults($query, $page, 10)
        ]);
    }
    
    /**
     * Search XHR
     *
     * @Route("/top/{string}", 
     *      name="top_search",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method("GET")
     */
    public function topSearchAction($string)
    {
        $em = $this->getDoctrine()->getManager();
         
        $collection = $em->getRepository('AppBundle:Composition')
                    ->getCompositionByString($string, 10);
        
        $response = [];
        $serializer = $this->get('jms_serializer');
        
        foreach ($collection as $item) {
            $response[] = $data[] = json_decode($serializer->serialize($item, 'json', SerializationContext::create()->setGroups(array('list'))));
        }
        
        return new JsonResponse([
            'success' => true,
            'data' => [
                'collection' => $response
            ]
        ]);
    }
    
        /**
     * Lists all Genre entities Ajax.
     *
     * @Route("/genres/{id}", 
     *      name="list_composition_genres",
     *      options={"expose"=true},
     *      condition="request.isXmlHttpRequest()"
     * )
     * @Method("GET")
     */
    public  function categoryGenresAction(CompositionCategory $category)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Genre')
                        ->findBy(['composition_type' => $category->getId()]);
        
        $response = [];
        $serializer = $this->get('jms_serializer');
        
        foreach ($entities as $item) {
            $response[] = json_decode($serializer->serialize($item, 'json'));
        }
        
        return new JsonResponse([
            'status' => 'success',
            'data' => $response
        ]);
    }
}
