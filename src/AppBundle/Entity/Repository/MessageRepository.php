<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use AppBundle\Entity\Message\MessageThread;

/**
 * Description of essageThreadRepository
 *
 * @author dimas
 */
class MessageRepository extends EntityRepository 
{
    public function getMessages(array $messages, $asArray = false, $orderBy = 'DESC') 
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m', 'r', 's')
            ->from('AppBundle:Message\Message', 'm')
            ->where("m.id IN(:messages)")
            ->orderBy('m.created', $orderBy)
            ->join('m.recipient', 'r')
            ->join('m.sender', 's')    
            ->setParameter('messages', $messages)
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true);
        
        
        return $asArray ? $qb->getArrayResult() : $qb->getResult();
    }
    
    public function getThreadMessages(MessageThread $thread, $orderBy = 'DESC'){
        
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m', 's')
            ->where('m.thread = :thread')
            ->from('AppBundle:Message\Message', 'm')
            ->join('m.sender', 's')    
            ->orderBy('m.created', $orderBy)
            ->setParameter('thread', $thread);    
    }
}
