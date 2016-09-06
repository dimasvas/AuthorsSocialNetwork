<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Oneup\UploaderBundle\Controller\BlueimpController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 * @Security("has_role('ROLE_USER')");
 */
class CustomUploaderController extends BlueimpController {
    
    public function upload()
    {
        $request = $this->getRequest();
        
        $composition_id = $request->request->get('composition_id');
        
        
        if(!$composition_id){
            throw new \Exception('No composition param');
        }
        
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $entity = $em->getRepository('AppBundle:Composition')->find($composition_id);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find Composition entity.');
        }
        
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        
        if(!$user->isEqualTo($entity->getUser())){
            throw new AccessDeniedException('You are not the owner of composition.');
        }

        $response = new EmptyResponse();
        
        $response->offsetSet('owner', $user);
        $response->offsetSet('composition', $entity);
        
        
        $files = $this->getFiles($request->files);

        $chunked = !is_null($request->headers->get('content-range'));

        foreach ((array) $files as $file) {
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
            } catch (UploadException $e) {
                $this->errorHandler->addException($response, $e);
            }
        }
        
       
        return $this->createSupportedJsonResponse($response->assemble());
    }
   
}
