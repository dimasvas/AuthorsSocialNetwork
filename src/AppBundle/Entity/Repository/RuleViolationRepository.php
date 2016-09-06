<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RuleViolationRepository
 *
 * @author dimas
 */
class RuleViolationRepository extends EntityRepository
{
    public function getList($order = 'ASC') 
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from('AppBundle:RuleViolation', 'r')
            ->orderBy('r.created', $order);
    }
    
    public function getRule($id) 
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('r')
            ->from('AppBundle:RuleViolation', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id)  
            ->getQuery()
            ->getOneOrNullResult();
    }
}
