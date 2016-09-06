<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Entity\Feedback;

/**
 * Feedback controller.
 *
 * @Route("/feedback")
 */
class FeedbackController extends Controller
{
    /**
     * Creates a new Feedback entity.
     *
     * @Route("/", 
     *      name="feedback_create",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Feedback();
        
        $message = filter_var($request->request->get('message'), FILTER_SANITIZE_STRING);
        $_token = $request->request->get('token');
                
        $entity->setName($request->request->get('name'));
        $entity->setEmail($request->request->get('email'));
        $entity->setMessage($message);
        
        $tokenIsValid = $this->isCsrfTokenValid('unknown', $_token);
        
        if(!$tokenIsValid) {
            throw new HttpException(400, 'Invalid');
        }
        
        $validator = $this->get('validator');
        
        $errors = $validator->validate($entity);
        
        if (count($errors) > 0) {
            throw new HttpException(400, (string) $errors);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse([
            'status' => 'success'
        ]);
    }
}
