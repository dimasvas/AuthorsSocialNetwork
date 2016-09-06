<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * Description of CompositionStatusRepository
 *
 * @author dimas
 */
class FavouriteCompositionRepository extends EntityRepository{
    
    public function getUserCompositions(User $user) 
    {
         return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:FavouriteComposition', 'c')
            ->where('c.user = :user_id')
            ->setParameter('user_id', $user->getId());
    }
    
    public function isRecordExits(User $user, $composition) 
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:FavouriteComposition', 'c')
            ->where('c.user = :user_id')
            ->andWhere('c.composition = :composition_id')    
            ->setParameter('user_id', $user->getId())
            ->setParameter('composition_id', $composition->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}
