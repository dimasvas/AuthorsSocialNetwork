<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\RuleViolation;


/**
 * Rating controller.
 *
 * @Route("/rule-violation")
 * @Security("has_role('ROLE_USER')");
 */
class RuleViolationController extends Controller {
    
    /**
     * Creates a new Rating entity.
     *
     * @Route("/", 
     *      name="rule_violation_create",
     *      options={"expose"=true})
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createAction(Request $request)
    {
        $text = $request->request->get('text');
        $type = $request->request->get('type');
        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
                
        //TODO: refactor
        if ($type == 'composition') {
            $entity = $em->getRepository('AppBundle:Composition')->find($id);
        } else if ($type == 'composition') {
            $entity = $em->getRepository('AppBundle:Commment')->find($id);
        } else {
            throw new Exception('Wrong type');
        }
        
        if(!$entity) {
            throw new Exception('Wrong type id');
        }
        
        $rule = new RuleViolation();
         
        $rule->setContent($text)
             ->setReporter($this->getUser())
             ->setStatus(RuleViolation::OPEN_STATUS)
             ->setType($type == 'composition' ? RuleViolation::COMPOSITION_TYPE : RuleViolation::COMMENT_TYPE)
             ->setEntityId($id);   
        
        //TODO: Validate
        
        $em->persist($rule);
        $em->flush();
        
        return new JsonResponse([
            'status' => 'success'
        ]);
    }
    
}
