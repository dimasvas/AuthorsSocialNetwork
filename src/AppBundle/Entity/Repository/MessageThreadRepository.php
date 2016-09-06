<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\User;
use Doctrine\ORM\Query;

/**
 * Description of essageThreadRepository
 *
 * @author dimas
 */
class MessageThreadRepository extends EntityRepository 
{
    private $threadsMax = 10;
    
    public function getArrayUserThreads(User $user, $withResults = false){
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->where('t.firstParticipant = :first_user')
            ->orWhere('t.secondParticipant = :second_user')
            ->from('AppBundle:Message\MessageThread', 't')
            ->setParameter('first_user', $user )
            ->setParameter('second_user', $user)
            ->orderBy('t.lastMessageCreated', 'DESC')
            ->setMaxResults($this->threadsMax);
        
        if($withResults) {
            return $qb->getQuery()
                    ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true) //Include foregn key
                    ->getArrayResult();
        }
        
        return $qb;
    }

    public function getORMUserThreads(User $user, $orderBy = 'DESC'){
        return  $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->where('t.firstParticipant = :first_user')
            ->orWhere('t.secondParticipant = :second_user')
            ->from('AppBundle:Message\MessageThread', 't')
            ->setParameter('first_user', $user )
            ->setParameter('second_user', $user)
            ->orderBy('t.lastMessageCreated', $orderBy);
    }
    
    public function getThread(User $firstUser, User $secondUser)
    {
        //TODO: USE HERE ANONIMOUS CLASS
        $participants = $this->getParticipantsOrder($firstUser, $secondUser);
        
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t')
            ->where('t.firstParticipant = :first_user')
            ->andWhere('t.secondParticipant = :second_user')
            ->from('AppBundle:Message\MessageThread', 't')
            ->setParameter('first_user', $participants['firstParticipant'] )
            ->setParameter('second_user', $participants['secondParticipant'])
            ->getQuery()    
            ->getOneOrNullResult();
    }
    
    private function getParticipantsOrder(User $firstUser, User $secondUser){
        $participants = [];
        
        if($firstUser->getId() < $secondUser->getId()){
            $participants['firstParticipant'] = $firstUser;
            $participants['secondParticipant'] = $secondUser;
        } else {
            $participants['firstParticipant'] = $secondUser;
            $participants['secondParticipant'] = $firstUser;
        }
        
        return $participants;
    }
}
