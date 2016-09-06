<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use\Doctrine\ORM\Query\Expr\Join;

/**
 * Description of CompositionRepository
 *
 * @author dimas
 */
class CompositionRepository extends EntityRepository 
{
    public function getPublished($authorUser)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c', 'g')
            ->from('AppBundle:Composition', 'c')
            ->where('c.author = :author')  
            ->andWhere('c.published = :published')
            ->andWhere('c.blocked = :blocked')
            ->innerJoin('c.genres', 'g')
            ->setParameter('author', $authorUser->getAuthor())
            ->setParameter('published', true)
            ->setParameter('blocked', false)
            ->getQuery()
            ->getResult();
    }
    
    public function getAllAuthors($authorUser) 
    {
         return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c', 'g')
            ->from('AppBundle:Composition', 'c')
            ->where('c.author = :author')     
            ->innerJoin('c.genres', 'g')
            ->setParameter('author', $authorUser->getAuthor())
            ->getQuery()
            ->getResult();
    }
    
    public function getByGenre($genre) 
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Composition', 'c')
            ->andWhere('c.published = :published')
            ->andWhere('c.blocked = :blocked')
            ->andWhere('c.archived = :archived')
            ->innerJoin('c.genres', 'g', Join::WITH, 'g.id = :genre')
            ->setParameter('genre', $genre->getId(), Type::INTEGER)
            ->setParameter('published', true)
            ->setParameter('blocked', false)
            ->setParameter('archived', false);
    }
    
    public function getByCategory($category) 
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Composition', 'c')
            ->where('c.category = :category')
            ->andWhere('c.published = :published')
            ->andWhere('c.blocked = :blocked')
            ->andWhere('c.archived = :archived')
            ->setParameter('category', $category->getId())
            ->setParameter('published', true)
            ->setParameter('blocked', false)
            ->setParameter('archived', false);
    }
    
    /**
     * Used in Admin Panel
     * @param type $type
     * @return type
     * @throws Exception
     */
    public function getCompositionByType($type)
    {
        $qb = $this->getEntityManager()
                ->createQueryBuilder();
        
        $qb->select('c')->from('AppBundle:Composition', 'c');
        
        switch ($type){
            case "published":
                $qb->where('c.blocked = :blocked')
                    ->setParameter('blocked', false);
                break;
            case "blocked":
                $qb->where('c.blocked = :blocked')
                    ->setParameter('blocked', true);
                break;
            default:
                throw new Exception("Wrong Parameter $type", 403);
        }
        
        return $qb;
    }
    
    public function getCompositionByString($search_string, $limit = 50){
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Composition', 'c')    
            ->where('upper(c.title) LIKE upper(:string)')  
            ->setParameter('string', '%'.$search_string.'%')
            ->setMaxResults( $limit )
            ->getQuery()
            ->getResult();
    }
    
    //TODO; USED in search
    public function getCompositionById($id){
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Composition', 'c')    
            ->where('c.id = :id')   
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    
    /**
     *  Used in CompositionTypeshow
     * @param type $id
     * @return type
     */
    public function getWithCategoryGenres($id){
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c', 'g', 'k')
            ->from('AppBundle:Composition', 'c')    
            ->where('c.id = :id')   
            ->innerJoin('c.genres', 'g')
            ->innerJoin('c.category', 'k')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function getUserActions () 
    {
//        return $this->getEntityManager()
//            ->createQueryBuilder()
//            ->select('c', 'g', 'k')
//            ->from('AppBundle:Composition', 'c')    
//            ->where('c.id = :id')   
//            ->innerJoin('c.genres', 'g')
//            ->innerJoin('c.category', 'k')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getOneOrNullResult();
    }
    
    /**
     * Used in search
     * @param type $params
     * @return type
     */
    public function getBySearchParams($params) {
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        
        $queryBuilder->select('c')
                ->from('AppBundle:Composition', 'c')
                ->where('upper(c.title) LIKE upper(:string)')
                ->andWhere('c.blocked = :blocked')
                ->andWhere('c.archived = :archived')
                ->setParameter('string', '%'.$params['text'].'%')
                ->setParameter('blocked', false)
                ->setParameter('archived', false);
        
        if (isset($params['category']) || array_key_exists('category', $params))
        {
            $queryBuilder->andWhere('c.category = :category')
                    ->setParameter('category', $params['category'], Type::INTEGER);
            
            if (isset($params['genre']) || array_key_exists('genre', $params))
            {
                $queryBuilder->innerJoin(
                        'c.genres', 'g', Join::WITH, 'g.id = :genre')
                        ->setParameter('genre', $params['genre'], Type::INTEGER);;
            }
        }
        
        if (isset($params['language']) || array_key_exists('language', $params))
        {
            $queryBuilder->andWhere('c.language = :language')
                    ->setParameter('language', $params['language'], Type::STRING);
        }
        
        if (isset($params['status']) || array_key_exists('status', $params))
        {
            $queryBuilder->andWhere('c.status = :status')
                    ->setParameter('status', $params['status'], Type::STRING);
        }
        
        return $queryBuilder;
    }
}
