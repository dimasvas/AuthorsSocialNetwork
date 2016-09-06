<?php

namespace AppBundle\Security;

use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Message\MessageThread;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class MessageThreadVoter extends Voter 
{
    const VIEW = 'view';
    
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW))) {
            return false;
        }

        if (!$subject instanceof MessageThread) {
            return false;
        }

        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            return false;
        }

        $thread = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($thread, $user);  
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView($thread, $user)
    {
        $firstUser = $thread->getFirstParticipant();
        $secondUser = $thread->getSecondParticipant();
        
        if ($user->isEqualTo($firstUser) || $user->isEqualTo($secondUser)) {
            return true;
        }
        
        return false;
    }

}
