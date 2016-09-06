<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Composition;

/**
 * Comment controller.
 * 
 * @Route("/{_locale}/comment", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 * @Security("has_role('ROLE_USER')");
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/parent/list/{composition}/{page}/", 
     *      name="comments_list", 
     *      defaults={"page" = 1},
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("GET")
     */
    public function indexParentAction(Composition $composition, $page)
    {
        $this->denyAccessUnlessGranted('auth_dependency_view', $composition);
        
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Comment')
                            ->getCompositionComments($composition->getId(), $this->getUser());

        $comments = $this->get('pager')->getPagedResults($queryBuilder, $page);
       
        $comments['url'] = $this->generateUrl('comments_list', ['composition' => $composition->getId(), 'page' => '#']);
                
        $comments['entities'] = $this->get('object_to_array_adapter')
                                ->commentsCollectionToArray($comments['entities'], $composition->getId());
       
        $comments['pager'] = $this->get('pager')->pagerToHtml($comments['pager'], $composition->getId());
      
        return new JsonResponse($comments);
    }
    
    /**
     * Lists all SubComment entities.
     *
     * @Route("/child/list/{composition}/{comment_id}/{page}", 
     *      name="subcomment_list", 
     *      defaults={"page" = 1},
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("GET")  
     */
    public function indexChildrenAction(Composition $composition, $comment_id, $page)
    {
        $this->denyAccessUnlessGranted('auth_dependency_view', $composition);
        
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Comment')
                            ->getCompositionSubComments($composition->getId(), $comment_id, $this->getUser());

        $comments = $this->get('pager')->getArrayPagedResults($queryBuilder, $page, $commentsPerPage = 5);
        
        
//        $comments['url'] = $this->generateUrl('subcomment_list', [
//                                                    'composition_id' => $composition_id, 
//                                                    'comment_id' => $comment_id,
//                                                    'page' => '#']
//                            );
        
        $comments['entities'] = $this->get('object_to_array_adapter')
                        ->commentsCollectionToArray($comments['entities'], $composition->getId()); 
                
        $latest = end($comments['entities']);
        
        $comments['url'] = $this->generateUrl('subcomment_more_list', [
                                            'composition' => $composition->getId(), 
                                            'comment_id' => $comment_id,
                                            'subcomment_id' => $latest['id']]
                    );       
      
        return new JsonResponse($comments);
    }
    
        /**
     * Lists all more SubComment entities.
     *
     * @Route("/replies/{composition}/{comment_id}/{subcomment_id}/", 
     *      name="subcomment_more_list", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("GET")  
     */
    public function moreChildrenAction(Composition  $composition, $comment_id, $subcomment_id)
    {
        $this->denyAccessUnlessGranted('auth_dependency_view', $composition);
        
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Comment')
                            ->getCompositionMoreSubComments($composition->getId(), $comment_id, $subcomment_id, $this->getUser());

        
        $comments = $this->get('pager')->getArrayPagedResults($queryBuilder, $page = 1, $maxPerPage = 2);
        
        $comments['entities'] = $this->get('object_to_array_adapter')
                        ->commentsCollectionToArray($comments['entities'], $composition->getId()); 
               
        $latest = end($comments['entities']);
        
        $comments['url'] = $this->generateUrl('subcomment_more_list', [
                                            'composition' => $composition->getId(), 
                                            'comment_id' => $comment_id,
                                            'subcomment_id' => $latest['id']]
                    );  
      
        return new JsonResponse($comments);
    }
    
    /**
     * Creates a new Comment entity.
     *
     * @Route("/parent/{id}", 
     *      name="comment_create", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createParentAction(Request $request, Composition $composition)
    { 
        $entity = new Comment();

        $entity->setUser($this->getUser());
        $entity->setContent($request->request->get('text'));
        $entity->setComposition($composition);
        $entity->setIsCompositionOwner($composition->getUser()->getId() == $this->getUser()->getId());
        
        $this->denyAccessUnlessGranted('create', $entity);

        //validation
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        
        $comment = $this->get('object_to_array_adapter')
                                ->commentEntityToArray($entity, $composition->getId());

        return new JsonResponse([
            'status' => 'success',
            'entities' => $comment
        ]);
    }
    
     /**
     * Creates a new SubComment entity.
     *
     * @Route("/child/{composition}/{parent_id}/", 
     *      name="subcomment_create",
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createChildrenAction(Request $request, Composition $composition, $parent_id)
    {   
        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository('AppBundle:Comment')->find($parent_id);

        if (!$parent) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }
        
        $entity = new Comment();
            
        $entity->setUser($this->getUser())
                ->setContent($request->request->get('text'))
                ->setComposition($composition)
                ->setIsCompositionOwner($composition->getUser()->getId() == $this->getUser()->getId())
                ->setParent($parent);
        
        $this->denyAccessUnlessGranted('create', $entity);
        
        //validate
        
        $parent->addChild($entity)
                        ->setHasResponse(true)
                        ->increaseResponseCount();
            
        $em->persist($entity);
        $em->persist($parent);
        
        $em->flush();
        
        $comment = $this->get('object_to_array_adapter')->commentEntityToArray($entity, $parent->getId());
        
        return new JsonResponse([
                'code' => 200,
                'status' => 'success',
                'entities' => $comment
            ]);
    }
    
    
    /**
     * Creates a form to create a Comment entity.
     *
     * @param Comment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comment $entity, $composition_id)
    {
        $form = $this->createForm('AppBundle\Form\CommentType', $entity, array(
            'action' => $this->generateUrl('comment_create', ['id' => $composition_id]),
            'method' => 'POST',
        ));

        return $form;
    }
    
    /**
     * Lists all Comment entities.
     *
     * @Route("/latest", 
     *      name="comments_latest_list", 
     *      condition="request.isXmlHttpRequest()",
     *      options={"expose"=true})
     * )
     * @Method("GET")
     */
    public function getLatestComments(){
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Comment')
                            ->getLatestComments();
        
        $comments = $this->get('pager')->getPagedResults($queryBuilder, $page = 1, $limit = 10);
        
        $comments['entities'] = $this->get('object_to_array_adapter')
                                    ->latestCommentsToArray($comments['entities']);
      
        return new JsonResponse([
            'status' => 'success',
            'data' => $comments['entities']
        ]);
    }
    
}
