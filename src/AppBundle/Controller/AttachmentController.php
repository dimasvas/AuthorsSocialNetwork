<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Composition;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Attachment controller.
 *
 * @Route("/attachment")
 */
class AttachmentController extends Controller
{     
    /**
     * Lists all Attachment entities.
     *
     * @Route("/list/{id}/{page}", 
     *      name="user-attachment-list", 
     *      defaults={"page" = 1}, 
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true})
     * )
     * @Method("GET")
     */
    public function indexAction(Composition $composition, $page)
    {
        //CompositionVoter
        $this->denyAccessUnlessGranted('view',  $composition);
         
        $em = $this->getDoctrine()->getManager();
        $queryBuilder  = $em->getRepository('AppBundle:Attachment')
                                            ->getCompositionAttachments($composition);
        //TODO: pagination
        $attachments = $this->get('pager')->getPagedResults($queryBuilder, $page, 20);
      
        $data = [];
        $serializer = $this->get('jms_serializer');
        
        //TODO: NO JsonDecode. Serializer returns no eval string. Need doc research.
        foreach($attachments['entities'] as $item) {
            $data[] = json_decode($serializer->serialize($item, 'json'));
        }
        
        return  new JsonResponse ([
            'isOwner' => $this->get('app.user_checker')->isOwnerAuthor($composition->getUser()),
            'composition_id' => $composition->getId(),
            'upload_dir' => $this->container->getParameter('attachment_dir'),
            'files' => $data    
        ]);
    }
  
    /**
     * Deletes a Attachment entity.
     *
     * @Route("/{id}", 
     *      name="attachment_delete",
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true}
     * )
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')");
     * @ParamConverter("attachment", class="AppBundle:Attachment")
     */
    public function deleteAction($attachment)
    {
        $composition = $attachment->getComposition();
        
        $this->denyAccessUnlessGranted('edit', $composition);
        
        $composition->removeAttachment($attachment);
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        return new JsonResponse(['message' => 'success']);
    }
}
