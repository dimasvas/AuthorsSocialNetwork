<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Message\MessageACL;
use UserBundle\Entity\User;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerBuilder;

/**
 * MessageACL controller.
 *
 * @Route("/message-acl")
 * @Security("has_role('ROLE_USER')");
 */
class MessageACLController extends Controller
{
    /**
     * Lists all MessageACL entities.
     *
     * @Route("/list/{page}", 
     *      name="message_acl",
     *      defaults={"page" = 1}, 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Message\MessageACL')
                                ->getList($this->getUser());
        //TODO: Critical pagination on front
        $paginator = $this->get('app.pagination')->getORMResults($queryBuilder, $page, 30);
        
        $serializer = SerializerBuilder::create()->build();
        
        $acl = $serializer->serialize(
                        $paginator->getCurrentPageResults()->getArrayCopy(), 'json', 
                        SerializationContext::create()->setGroups(array('default')));

        return new JsonResponse(
                array(
                    'success' => true,
                    'collection' => $acl,
                    'pagination' => array(
                            'hasNextPage' => $paginator->hasNextPage(), 
                            'nextPage' => $paginator->hasNextPage() ? $paginator->getNextPage() : null
                    )
                )
            );
    }
    /**
     * Creates a new MessageACL entity.
     *
     * @Route("/create/{blocked}", 
     *      name="message_acl_create",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("POST")
     */
    public function createAction(User $blocked)
    {
        $em = $this->getDoctrine()->getManager();
        
        $recordExists = $em->getRepository('AppBundle:Message\MessageACL')
                    ->findBy(['owner' => $this->getUser(), 'blocked' => $blocked]);
        
       //TODO: Add voter
        if($recordExists) {
            return new JsonResponse(['successs' => true]);
        }

        $entity = (new MessageACL())
                ->setOwner($this->getUser())
                ->setBlocked($blocked);

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
    
    /**
     * Delete entity from ACl.
     *
     * @Route("/delete/{blocked}", 
     *      name="message_acl_delete",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("DELETE")
     */
    public function deleteAction (User $blocked) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $record = $em->getRepository('AppBundle:Message\MessageACL')
                    ->findOneBy(['owner' => $this->getUser(), 'blocked' => $blocked]);
        
        if(!$record) {
            return new JsonResponse(['success' => false]);
        }
        
        $em->remove($record);
        $em->flush();
        
        return new JsonResponse(['success' => true]);
    }
}
