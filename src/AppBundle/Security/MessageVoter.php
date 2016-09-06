<?php

namespace AppBundle\Security;

use AppBundle\Entity\Message\Message;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Doctrine\ORM\EntityManager;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class MessageVoter extends Voter 
{
    const CREATE = 'create';
    const VIEW = 'view';
    const DELETE = 'delete';
    
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::CREATE, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Message) {
            return false;
        }

        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $message = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView();    
            case self::CREATE:
                return $this->canCreate($message, $user);
            case self::DELETE:
                return $this->canDelete();     
        }

        throw new \LogicException('This code should not be reached!');
    }
    
    private function canCreate($message, $user) 
    {
       
        if(!$this->checkBlocked($message, $user)){
            return true;
        }
        
        return false;
    }
    
    private function canDelete() 
    {
        return false;
    }

    private function canView()
    {
        return false;
    }
    
    private function checkBlocked ($message, $user) 
    {
        $blocked = $this->em->getRepository('AppBundle:Message\MessageACL')
                        ->findBy(['owner' => $message->getRecipient(), 'blocked' => $user->getId()]);
        
        return $blocked ? true : false;
    }

}
