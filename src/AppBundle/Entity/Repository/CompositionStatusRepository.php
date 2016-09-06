<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\CompositionStatus;

/**
 * Description of CompositionStatusRepository
 *
 * @author dimas
 */
class CompositionStatusRepository extends EntityRepository
{
    
    public function getInProcessStatus() {
         return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:CompositionStatus', 'c')
            ->where('c.alias = :alias')
            ->setParameter('alias', CompositionStatus::IN_PROCESS_STATUS_NAME )     
            ->getQuery()
            ->getSingleResult();
    }
    
    public function getFinishedStatus() {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:CompositionStatus', 'c')    
            ->where('c.alias = :alias')
             ->setParameter('alias', CompositionStatus::FINISHED_STATUS_NAME )   
            ->getQuery()
            ->getSingleResult();
    }
}
