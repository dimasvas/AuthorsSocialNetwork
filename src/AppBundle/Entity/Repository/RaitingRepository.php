<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of RaitingRepository
 *
 * @author dimas
 */
class RaitingRepository extends EntityRepository 
{
    public function getCompositionStat($composition)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('AVG(r.rate) AS total_rating, COUNT(r.id) AS hits')
            ->from('AppBundle:Rating', 'r')
            ->where('r.composition = :composition')
            ->groupBy('r.composition')    
            ->setParameter('composition', $composition)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
