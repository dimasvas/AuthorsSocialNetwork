<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;

/**
* @Route("/users")
* @Security("has_role('ROLE_ADMIN')");
*/
class UserController extends Controller
{
    /**
     * @Route("/list/{type}/{page}", name="admin-users-get", defaults={"page" = 1})
     * @Method("GET")
     */
    public function typeAction($type, $page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pagerfanta = $this->get('pager')->getResults(
            $em->getRepository('UserBundle:User')->getUsers($type), 
            $page,
            $maxPerPage = 20
        );
        
        return $this->render(
            'AdminBundle:User:index.html.twig',
            array('pager' => $pagerfanta, 'type' => $type)
        );
    }
    
    /**
     * @Route("/search", name="admin-users-search-page")
     * @Method("GET")
     */
    public function searchPageAction()
    {
        return $this->render(
            'AdminBundle:User:search.html.twig',
            array()
        );
    }
    
    /**
     * @Route("/search/{type}", name="admin-users-search", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function searchAction(Request $request, $type)
    {
        $data = $request->request->get('data');
        
        if(!$data){
            throw new \Exception("No Required Parameter");
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $data = $type == 'id' ? $data =  $em->getRepository('UserBundle:User')->getUserById($data) :
            $em->getRepository('UserBundle:User')->getUserByString($data);
        //TODO: remove custom serializer. Use JMS instead.
        $data = $this->get('app.user_serializer_service')->serializeCollection($data);
        
        //TODO: Wrong behaviour
        return new JsonResponse(['data' => $data]);
    }
    
    /**
     * @Route("/profile/{id}", name="admin-users-profile")
     * @Method("GET")
     */
    public function profileAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity =  $em->getRepository('UserBundle:User')->find($id);
        
        if(!$entity){
            throw new NotFoundHttpException('User not found');
        }
        return $this->render(
            'AdminBundle:User:profile.html.twig',
            array('entity' => $entity)
        );
    }
    
    /**
     * @Route("/block/{id}", name="admin-users-block", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function blockAction(Request $request, $id)
    {
        $blocked = $request->request->get('blocked');
        
        if(!$blocked ){
            throw new \Exception("No Required Parameter");
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $entity =  $em->getRepository('UserBundle:User')->find($id);
        
        if(!$entity){
            throw new NotFoundHttpException('User not found');
        }
        
        $entity->setLocked($blocked);
        $em->persist($entity);
        $em->flush();
        
        return new JsonResponse(['response' => 'success']);
    } 
    
    /**
     * @Route("/author/{id}", name="admin-author-compositions")
     * @Method("GET")
     */
    public function getCompositionsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        try {
            $author = $em->getRepository('AppBundle:Author')->find($id);
        } catch (NoResultException $e) {
            throw new NotFoundHttpException('No such author');
        }
        
        $compositions = $em->getRepository('AppBundle:Composition')->findByAuthor($id);
        
        $data = $serializer = $this->get('jms_serializer')->serialize($compositions, 'json');
        
        return new Response($data);
    }
}
