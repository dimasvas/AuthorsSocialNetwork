<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializationContext;

/**
* @Route("/composition")
* @Security("has_role('ROLE_ADMIN')");
*/
class CompositionController extends Controller
{
     /**
     * @Route("/list/{type}/{page}", name="admin-composition-list-get", defaults={"page" = 1})
     * @Method("GET")
     */
    public function indexAction($type, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pagerfanta = $this->get('pager')->getResults(
            $em->getRepository('AppBundle:Composition')->getCompositionByType($type), 
            $page,
            $maxPerPage = 20
        );
        
        return $this->render(
            'AdminBundle:Composition:index.html.twig',
            array('pager' => $pagerfanta, 'type' => $type)
        );
    }
    
    /**
     * @Route("/show/{id}", name="admin-composition-show")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Composition')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Composition entity.');
        }
        
        return $this->render(
            'AdminBundle:Composition:show.html.twig',
            array('entity' => $entity)
        );
    }
    
     /**
     * @Route("/search", name="admin-composition-search-page")
     * @Method("GET")
     */
    public function searchPageAction()
    {
        return $this->render(
            'AdminBundle:Composition:search.html.twig',
            array()
        );
    }
    
    /**
     * @Route("/search/{type}", name="admin-composition-search")
     * @Method("POST")
     */
    public function searchAction(Request $request, $type)
    {
        $data = $request->request->get('data');
        
        if(!$data){
            throw new \Exception("No Required Parameter");
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $compositions = $type == 'id' ? $data =  $em->getRepository('AppBundle:Composition')->getCompositionById($data) :
             $em->getRepository('AppBundle:Composition')->getCompositionByString($data);
        
        $data = $serializer = $this->get('jms_serializer')
                    ->serialize($compositions, 'json', SerializationContext::create()->setGroups(array('admin-search')));
        
        //TODO: Wrong behaviour
        return new JsonResponse(['data' => json_decode($data)]);
    }
    
     /**
     * @Route("/block/{id}", name="admin-composition-block", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function blockAction(Request $request, $id)
    {
        $blocked = $request->request->get('blocked');
        
        if(!$blocked ){
            throw new \Exception("No Required Parameter");
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppBundle:Composition')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Composition entity.');
        }
        
        $entity->setBlocked($blocked);
        
        $em->persist($entity);
        $em->flush();
        
        return new JsonResponse(['response' => 'success']);
    } 
}
