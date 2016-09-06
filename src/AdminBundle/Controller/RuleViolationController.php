<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\Serializer\SerializationContext;
use AppBundle\Entity\RuleViolation;

/**
 * Description of RuleViolationController
 *
 * @Route("/rule-violation")
 * @Security("has_role('ROLE_ADMIN')"); 
 */
class RuleViolationController  extends Controller
{    
    /**
     * @Route("/list/{page}", name="admin-rviolation-list", defaults={"page" = 1})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pagerfanta = $this->get('pager')->getResults(
            $em->getRepository('AppBundle:RuleViolation')->getList(), 
            $page,
            $maxPerPage = 20
        );
        
        
        return $this->render(
            'AdminBundle:Rviolation:index.html.twig',
            array('pager' => $pagerfanta)
        );
    }
    
    /**
     * @Route("/show/{id}", name="admin-rviolation-show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppBundle:RuleViolation')->getRule($id);
        
        $composition = $em->getRepository('AppBundle:Composition')->find($entity->getEntityId());
        
        return $this->render(
            'AdminBundle:Rviolation:show.html.twig',
            array('entity' => $entity, 'composition' => $composition)
        );
    }
}
