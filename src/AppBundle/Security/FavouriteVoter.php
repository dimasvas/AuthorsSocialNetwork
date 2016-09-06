<?php

namespace AppBundle\Security;

use AppBundle\Entity\Composition;
use AppBundle\Entity\FavouriteComposition;
use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class FavouriteVoter extends Voter 
{    
    const CREATE = 'create';
    const DELETE = 'delete';
    const VIEW = 'view';
    
    private $em;
        
    public function __construct(EntityManager $em) 
    {
        $this->em = $em;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::CREATE, self::VIEW, self::DELETE))) {
            return false;
        }

        // only vote on Composition objects inside this voter
        if (!$subject instanceof FavouriteComposition) {
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
        
        //The user must not be blocked
        if ($user->isLocked()) {
            return false;
        }

        // you know $subject is a FavouriteComposition object, thanks to supports
        /** @var FavouriteComposition favoutiteComposition */
        $favoutiteComposition = $subject;
        $composition = $favoutiteComposition->getComposition();
        
        //The composition must be Unlocked
        if($composition->getBlocked()) {
            return false;
        }

        switch($attribute) {
            case self::VIEW:
                return $this->canView($composition, $user);
            case self::CREATE:
                return $this->canCreate($composition, $user);    
            case self::DELETE: 
                return $this->canDelete($favoutiteComposition, $user); 
        }

        throw new \LogicException('This code should not be reached!');
    }
    
    private function canCreate(Composition $composition, User $user) 
    {     
        // The composition owner can't add to favourite
        if ($user->isEqualTo($composition->getUser())) {
            return false;
        }
        
        //Check if record Exists
        $favourite_record = $this->em->getRepository('AppBundle:FavouriteComposition')
                            ->isRecordExits($user, $composition);
        
        if($favourite_record) {
            return false;
        }
        
        return true;
    }

    private function canView(Composition $composition, User $user)
    {   
        return true;
    }
    
    private function canDelete(FavouriteComposition $favourite, User $user) 
    {
        return $user->isEqualTo($favourite->getUser());
    }

}
