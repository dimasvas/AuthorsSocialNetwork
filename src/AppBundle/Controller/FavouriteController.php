<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\FavouriteComposition;
use AppBundle\Entity\Composition;
use \Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Favourite controller.
 *
 * @Route("/{_locale}/favourite", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 * @Security("has_role('ROLE_USER')");
 */
class FavouriteController extends Controller
{

    /**
     * Lists all FavouriteComposition entities.
     *
     * @Route("/list/{page}", name="favourite_list", defaults={"page" = 1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:FavouriteComposition')
                           ->getUserCompositions($this->getUser());

        return $this->get('pager')->getPagedResults($queryBuilder, $page);
    }
    
    /**
     * Creates a new FavouriteComposition entity.
     *
     * @Route("/{id}", 
     *      name="favourite_create",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createAction(Request $request, Composition $composition)
    {
        $entity = new FavouriteComposition();
        
        $entity->setComposition($composition)
                ->setUser($this->getUser())
                ->setComment($request->request->get('text'));
        
        $this->denyAccessUnlessGranted('create',  $entity);
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse([
            'code' => 200,
            'status' => 'success'
        ]);
    }

    /**
     * Deletes a FavouriteComposition entity.
     *
     * @Route("/{id}", 
     *      name="favourite_delete",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')");
     */
    public function deleteAction(FavouriteComposition $entity)
    {   
        $this->denyAccessUnlessGranted('delete',  $entity);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return new JsonResponse([
            'code' => 200,
            'status' => 'success'
        ]);
    }
}
