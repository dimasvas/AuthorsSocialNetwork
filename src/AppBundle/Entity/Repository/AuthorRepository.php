<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of AuthorRepository
 *
 * @author dimas
 */
class AuthorRepository extends EntityRepository {

    public function getAuthors($order = 'ASC')
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u', 'a')
            ->where('u.isAuthor = :isAuthor')
            ->andWhere('u.enabled = :isEnabled')
            ->andWhere('u.locked = :isLocked')
            ->from('UserBundle:User', 'u')
            ->join('u.author', 'a')
            ->setParameter('isAuthor', 1)    
            ->setParameter('isEnabled', 1) 
            ->setParameter('isLocked', 0); 
//            ->orderBy('u.created', $order);
    }

    public function getAuthor($id)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u', 'a')
            ->where('u.isAuthor = :isAuthor')
            ->andWhere('u.enabled = :isEnabled')
            ->andWhere('u.id = :id')
            ->from('UserBundle:User', 'u')
            ->join('u.author', 'a')
            ->setParameter('id', $id)
            ->setParameter('isAuthor', 1)    
            ->setParameter('isEnabled', 1)     
            ->getQuery()
            ->getSingleResult();
    }
}
