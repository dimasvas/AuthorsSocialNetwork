<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Composition;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Update controller.
 *
 * @Route("/composition-update")
 * @Security("has_role('ROLE_USER')");
 */
class UpdateController extends Controller
{
    /**
     * Lists all CompositionUpdate entities.
     *
     * @Route("/{page}", 
     *      name="composition-update-list", 
     *      defaults={"page"=1}, 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true}
     * )
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:CompositionUpdate')
                            ->getUpatesQuery($this->getUser());
        
        $updates = $this->get('pager')->getPagedResults($queryBuilder, $page, 2);
        
        $updates['entities'] = $this->get('app.composition_update_serializer')
                                        ->collectionSerialize($updates['entities']);
        
        $updates['pager'] = $this->get('pager')->updatePagerToHtml($updates['pager']);
        
        return new JsonResponse($updates);
    }
    
    /**
     * Deletes a CompositionUpdate entity.
     *
     * @Route("/{id}", 
     *      name="composition_update_delete", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:CompositionUpdate')->find($id);

        /**
         * TODO: ADD SECURITY CHECK OWNER
         */
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompositionUpdate entity.');
        }

        $em->remove($entity);
        $em->flush();
        
        return new JsonResponse([
            'code' => 200,
            'status' => 'success'
        ]);
    }
    
    /**
     * Send Composition Update to subscribers.
     *
     * @Route("/update/{id}", 
     *      name="comp_subscriptions_update", 
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true})
     * @Method("POST")
     */
    public function broadcastAction(Request $request, Composition $composition)
    {   
        if ($composition->getBlocked() == true) {
            throw new \Exception('Composition is blocked.');
        }
        
        if ($composition->getUser()->getId() != $this->getUser()->getId()) {
            throw new \Exception('You are not the owner of the composition. You can\'t send update.');
        }     
        
        $em = $this->getDoctrine()->getManager();
                
        $subscribtion = $em->getRepository('AppBundle:CompositionSubscription')
                        ->findOneBy(['composition' => $composition->getId()]);
        
        if(!$subscribtion) {
            return new JsonResponse([
                'code' => 400,
                'status' => 'error',
                'msg' => 'no_subscribers'
            ]);
        }
        
        /**
         * TODO
         * secure add new ROKE_AUTHOR
         * Only author can access some controllers/ ootherwise it will be an error user->getAuthor()
         * 
         * Check if author has gidden his name/
         */
        $this->get('old_sound_rabbit_mq.composition_send_update_producer')
            ->publish([
                'composition_id' => $composition->getId(),
               // 'composition_img' => $composition->getImageFile(),
                'composition_title' => $composition->getTitle(),
                'author_name' => $this->getUser()->getName() . ' ' .$this->getUser()->getSurname(),
                'owner' => $this->getUser()->getId(),
                'message' => $request->request->get('message')
        ]);
        
        
        return new JsonResponse([
            'code' => 200,
            'status' => 'success'
        ]);
    }
}
