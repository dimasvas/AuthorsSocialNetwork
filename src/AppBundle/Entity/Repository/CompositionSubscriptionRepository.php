<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * Description of CompositionSubscriptionRepository
 *
 * @author dimas
 */
class CompositionSubscriptionRepository extends EntityRepository {
    
    public function getUserSubscriptions(User $user) 
    {
         return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('s')
            ->from('AppBundle:CompositionSubscription', 's')
            ->where('s.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->orderBy('s.created', 'ASC');
    }
}
