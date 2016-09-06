<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FeedbackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeedbackRepository extends EntityRepository
{
    public function getFeedbacks($order = 'ASC')
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('f')
            ->from('AppBundle:Feedback', 'f')
            ->orderBy('f.created', $order);
    }
}
