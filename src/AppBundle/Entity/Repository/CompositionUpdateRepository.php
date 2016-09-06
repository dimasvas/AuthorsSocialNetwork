<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of CompositionUpdateRepository
 *
 * @author dimas
 */
class CompositionUpdateRepository extends EntityRepository {
    
    public function getUpatesQuery($user){
        
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from('AppBundle:CompositionUpdate', 'u')
            ->where('u.user = :user_id')
            ->setParameter('user_id', $user->getId());
    }
}
