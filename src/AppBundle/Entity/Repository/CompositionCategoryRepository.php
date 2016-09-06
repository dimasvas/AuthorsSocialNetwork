<?php
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Cache\RedisCache;
use Predis\Client;

/**
 * Description of CategoryRepository
 *
 * @author dimas
 */
class CompositionCategoryRepository extends EntityRepository
{
    public function getFullCategories()
    {
        $cache_lifetime = 7200;
      
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c', 'g')
            ->from('AppBundle:CompositionCategory', 'c')
            ->innerJoin('c.genres', 'g')
            ->getQuery()
            ->useResultCache(true, $cache_lifetime)
            ->getArrayResult();
    }
}
