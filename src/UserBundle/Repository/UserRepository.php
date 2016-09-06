<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 * @package UserBundle\Repository
 */
class UserRepository extends EntityRepository
{
    public function isUsernameExists($username) {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->where('upper(u.username) = upper(:username)')
            ->from('UserBundle:User', 'u')
            ->setParameter('username', $username)
            ->getQuery()
            ->getResult();
    }
    
    public function getUsers($type){
        $qb = $this->getEntityManager()
                ->createQueryBuilder();
        
        $qb->select('u')->from('UserBundle:User', 'u');
        
        switch ($type){
            case "active":
                $qb->where('u.enabled = :enabled')
                    ->andWhere('u.locked = :locked')
                    ->setParameter('enabled', true)
                    ->setParameter('locked', false);
                break;
            case "nonactive":
                $qb->where('u.enabled = :enabled')
                    ->setParameter('enabled', false);
                break;
            case "blocked":
                $qb->where('u.locked = :locked')
                    ->setParameter('locked', true);
                break;
            default:
                throw new Exception("Wrong Parameter $type", 403);
        }
        
        return $qb;
    }
    
    public function getUserByString($search_string){
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from('UserBundle:User', 'u')    
            ->where('upper(u.username) LIKE upper(:string)')
            ->orWhere('upper(u.name) LIKE upper(:string)') 
            ->orWhere('upper(u.surname) LIKE upper(:string)')  
            ->orWhere('upper(u.aliasName) LIKE upper(:string)')     
            ->setParameter('string', '%'.$search_string.'%')
            ->getQuery()
            ->getResult();
    }
    
    public function getUserById($id){
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from('UserBundle:User', 'u')    
            ->where('u.id = :id')   
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    
//    public function getAuthors($order = 'ASC')
//    {
//        return $this->getEntityManager()
//            ->createQueryBuilder()
//            ->select('u')
//            ->where('u.isAuthor = 1')
//            ->andWhere('u.enabled = 1')
//            ->andWhere('u.locked = 0')
//            ->from('UserBundle:User', 'u');
////            ->orderBy('u.created', $order);
//    }
//
//    public function getAuthor($id)
//    {
//        return $this->getEntityManager()
//            ->createQueryBuilder()
//            ->select('u')
//            ->where('u.isAuthor = 1')
//            ->andWhere('u.enabled = 1')
//            ->andWhere('u.id = :id')
//            ->from('UserBundle:User', 'u')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getSingleResult();
//    }
}