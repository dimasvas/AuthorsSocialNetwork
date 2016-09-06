<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;

/**
 * Description of AttachmentRepository
 *
 * @author dimas
 */
class MessageACLRepository extends EntityRepository {
    
    public function getList(User $owner, $order = 'ASC'){
        
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a', 'b')
            ->where('a.owner = :owner')
            ->from('AppBundle:Message\MessageACL', 'a')
            ->join('a.blocked', 'b')
            ->setParameter('owner', $owner)
            ->orderBy('a.created', $order);
    }
}
