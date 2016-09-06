<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\CompositionSubscription;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Event\CompositionUpdateEvent;
use AppBundle\AppEvents;
use AppBundle\EventSubscriber\CompositionSubscriber;
use AppBundle\Entity\Composition;

/**
 * Subscription controller.
 *
 * @Route("/{_locale}/subscriptions", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 * @Security("has_role('ROLE_USER')");
 */
class SubscriptionController extends Controller
{
    /**
     * Lists all CompositionSubscription entities.
     *
     * @Route("/page/{page}", 
     *      name="subscribtion_page", 
     *      defaults={"page" = 1}
     * )
     * @Method("GET")
     */
    public function pageAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:CompositionSubscription')
                           ->getUserSubscriptions($this->getUser());
        
        return $this->render(
            'AppBundle:CompositionSubscription:index.html.twig'
        );
    }
    
    /**
     * Lists all CompositionSubscription entities.
     *
     * @Route("/{page}", 
     *      name="subscribtion_list", 
     *      defaults={"page" = 1}, 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:CompositionSubscription')
                            ->getUserSubscriptions($this->getUser());
        
        
        
        $updates = $this->get('pager')->getPagedResults($queryBuilder, $page, 2);
        
        $updates['entities'] = $this->get('app.composition_subscribtion_serializer')->collectionSerialize($updates['entities']);
        
        $updates['pager'] = $this->get('pager')->subscribtionPagerToHtml($updates['pager']);
        
        return new JsonResponse($updates);
    }
    
    /**
     * Creates a new CompositionSubscription entity.
     *
     * @Route("/{id}", 
     *      name="subscribtion_create", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("POST")
     */
    public function createAction(Composition $composition)
    {   
        $entity = new CompositionSubscription();
        
        $entity->setUser($this->getUser())
                ->setComposition($composition);
        
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
     * Deletes a CompositionSubscription entity.
     *
     * @Route("/{id}", 
     *      name="subscribtion_delete",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("DELETE")
     */
    public function deleteAction(CompositionSubscription $entity)
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
