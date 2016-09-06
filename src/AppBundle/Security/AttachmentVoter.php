<?php

namespace AppBundle\Security;

use AppBundle\Entity\Composition;
use AppBundle\Entity\Attachment;
use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class AttachmentVoter extends Voter 
{    
    const CREATE = 'create';
    const VIEW = 'view';
    
    private $em;
        
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::CREATE, self::VIEW))) {
            return false;
        }

        // only vote on Composition objects inside this voter
        if (!$subject instanceof Attachment) {
            return false;
        }

        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        $attachment = $subject;
        $composition = $attachment->getComposition();
        
        //The composition must be Unlocked
        if($composition->getBlocked()) {
            return false;
        }

        switch($attribute) {
            case self::VIEW:
                return $this->canView($composition, $user);
            case self::CREATE:
                return $this->canCreate($composition, $user);    
        }

        throw new \LogicException('This code should not be reached!');
    }
    
    private function canCreate(Composition $composition, User $user) 
    {   
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        //The user must not be blocked
        if ($user->isLocked()) {
            return false;
        }
        
        // Only compossition owner can add attachments
        if (!$user->isEqualTo($composition->getUser())) {
            return false;
        }
        
        return true;
    }

    private function canView(Composition $composition, User $user)
    {   
        if($this->canCreate($composition, $user)) {
            return true;
        }
        
        if(!$composition->getPublished()){
            return false;
        }

        return true;
    }

}
