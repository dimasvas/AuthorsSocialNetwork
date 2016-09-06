<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Composition;

/**
 * Composition controller.
 *
 * @Route("/image")
 */
class ImageController extends Controller 
{
     /**
     * 
     * @param string $type
     * 
     * @Route("/update/profile", 
     *     name="img_update_profile", 
     *     condition="request.isXmlHttpRequest()", 
     *     options={"expose"=true}
     *)
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')");
     */
    public function updateProfileAction(Request $request)
    {
        try{
            $handler = $this->get('app.ajax_upload_image_handler');
            $handler->processFileFromRequest('profile');
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
        
        $user = $this->getUser();
        
        $handler->setCurrentFile($user->getImageName());
        
        $user->setImageName($handler->getNewFileName());
        
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
                        
        try {
            $em->persist($user);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $exception) {
            $em->getConnection()->rollBack();
            $handler->deleteNewFile();
            
            echo $exception->getMessage();
        }
        
        $handler->deleteCurrentFile();
        
        
        return new JsonResponse([
            'success' => true,
            'data' => [
                'img' => $handler->getRelativeFile()
            ]
        ]);
    }
    
    /**
     * 
     * @param string $type
     * 
     * @Route("/update/composition/{id}", name="img_update_composition", condition="request.isXmlHttpRequest()", options={"expose"=true})
     * @ParamConverter("composition", class="AppBundle:Composition")
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')");
     */
    public function updateCompositionAction(Composition $composition)
    {
        $this->denyAccessUnlessGranted('edit', $composition);
        
        try{
            $handler = $this->get('app.ajax_upload_image_handler');
            $handler->processFileFromRequest('composition');
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        $handler->setCurrentFile($composition->getImageName());
        
        $composition->setImageName($handler->getNewFileName());
        
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
                        
        try {
            $em->persist($composition);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $exception) {
            $em->getConnection()->rollBack();
            $handler->deleteNewFile();
            
            echo $exception->getMessage();
        }
        
        $handler->deleteCurrentFile();
        
        return new JsonResponse([
            'success' => true,
            'data' => [
                'img' => $handler->getRelativeFile()
            ]
        ]);
    }
    
}
