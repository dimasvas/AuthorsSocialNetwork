<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;
use Doctrine\DBAL\Types\Type;
use\Doctrine\ORM\Query\Expr\Join;

/**
 * Description of CommentRepository
 *
 * @author dimas
 */
class CommentRepository  extends EntityRepository
{
    public function getCompositionComments($composition_id, $user, $order = 'DESC') {

        $queryBulder = $this->getCommonQuery($composition_id, $user, $order);
        
        return $queryBulder->andWhere('c.parent is NULL');
    }
    
    public function getCompositionSubComments($composition_id, $comment_id, $user, $order = 'DESC') {

        $queryBulder = $this->getCommonQuery($composition_id, $user, $order);
        
        return $queryBulder->andWhere('c.parent =:parent')
                           ->setParameter('parent', $comment_id); 
    }
    
    public function getCompositionMoreSubComments($composition_id, $comment_id, $subcomment_id, $user) {
         
        $queryBulder = $this->getCommonQuery($composition_id, $user, 'DESC');
        
        return $queryBulder->andWhere('c.parent =:parent')
                            ->andWhere('c.id < :subcomment_id') 
                            ->setParameter('parent', $comment_id)
                            ->setParameter('subcomment_id', $subcomment_id); 
    }
    
    public function getLatestComments($order = 'DESC'){
        $querBuilder = $this->getEntityManager()->createQueryBuilder();
        
        $querBuilder->select('c')
            ->from('AppBundle:Comment', 'c')
            ->andWhere('c.isBlocked = :is_blocked')
            ->setParameter('is_blocked', false)     
            ->orderBy('c.created', $order);
        
        return $querBuilder;
    }
    
    private function getCommonQuery($composition_id, $user, $order) {
        
        $querBuilder = $this->getEntityManager()->createQueryBuilder();
        
        $querBuilder->select('c')
            ->from('AppBundle:Comment', 'c')
            ->where('c.composition = :composition_id')
            ->andWhere('c.isBlocked = :is_blocked')
            ->setParameter('composition_id', $composition_id)
            ->setParameter('is_blocked', false)     
            ->orderBy('c.created', $order);
        
        if($user instanceof User) {
            $querBuilder->leftJoin('c.vote', 'v', Join::WITH, 'v.user = :user');
            $querBuilder->setParameter('user', $user->getId(), Type::INTEGER);
        }
        
        return $querBuilder;
    }
}
