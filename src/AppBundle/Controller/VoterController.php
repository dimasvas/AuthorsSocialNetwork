<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Vote;


/**
 * Rating controller.
 *
 * @Route("/vote")
 */
class VoterController extends Controller {
    
    /**
     * Creates a new Vote entity.
     *
     * @Route("/{comment_id}", 
     *      name="vote_create",
     *      options={"expose"=true})
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createAction(Request $request, $comment_id)
    {
        $type = $request->request->get('type');
        
        if(!in_array($type, array('like', 'dislike'))){
            throw new Exception('Wrong vote type.');
        }
        
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->find($comment_id);
        
        if(!$comment) {
            throw new Exception('No such comment entity');
        }
        
        $savedVote = $em->getRepository('AppBundle:Vote')
                            ->findBy(['user' => $this->getUser(), 'comment' => $comment]);
        
        if($savedVote) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Vote exists'    
            ]);
        }
        
        if($comment->getUser()->isEqualTo($this->getUser())) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Comment owner can not vote.'    
            ]);
        }
        
        $this->setVote($em, $this->getUser(), $comment, $type);
        
        $this->setFrienship($em, $type, $comment);
        
//         $this->get('old_sound_rabbit_mq.user_vote_producer')
//            ->publish([
//                'commentId' => $comment->getId(),
//                'voterUser' => $this->getUser()->getId(),
//                'type' => $type
//        ]);
        
        return new JsonResponse([
            'status' => 'success'
        ]);
    }
    
    
    private function setVote($em, $voterUser, $comment, $type) 
    {
        $vote = new Vote();
        
        $vote->setUser($voterUser)
             ->setComment($comment)
             ->setType($type);   
            
        if($type == 'like') {
            $vote->setLike();
            $comment->increaseVote();
        } else {
            $vote->setDislike();
            $comment->decreaseVote();
        }
        
        $em->persist($vote);
        $em->persist($comment);
        $em->flush();
    }
    
    private function setFrienship($em, $type, $comment) 
    {
        $commentOwner = $comment->getUser();
        
        $prevValue = $commentOwner->getFriendship();
        
        if($type == 'like'){
            $newValue = ($prevValue + 1);
        }else {
            $newValue = ($prevValue - 1);
        }
        
        $commentOwner->setFriendship($newValue);
         
        $em->persist($commentOwner);
        $em->flush();
    }
}
