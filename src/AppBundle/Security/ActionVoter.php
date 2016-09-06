<?php

namespace AppBundle\Security;

use AppBundle\Entity\Composition;
use AppBundle\Entity\CompositionAction;
use UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class ActionVoter extends Voter 
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
       
        if (!$subject instanceof CompositionAction) {
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
        $compositionAction = $subject;
        $composition = $compositionAction->getComposition();
        
        //The composition must be Unlocked
        if($composition->getBlocked()) {
            return false;
        }

        switch($attribute) {
            case self::VIEW:
                return $this->canView($composition, $user);
            case self::CREATE:
                return $this->canCreate($compositionAction, $composition, $user);    
            case self::DELETE: 
                return $this->canDelete($compositionAction, $user); 
        }

        throw new \LogicException('This code should not be reached!');
    }
    
    private function canCreate(CompositionAction $action, $composition, $user) 
    {     
        // The composition owner can't add to favourite
        if ($user->isEqualTo($composition->getUser())) {
            return false;
        }
        
        if(!$this->getCheckRecord($action, $user)) {
            return false;
        }
        
        return true;
    }

    private function canView(Composition $composition, User $user)
    {   
        return true;
    }
    
    private function canDelete(CompositionAction $action, User $user) 
    {
        return $user->isEqualTo($action->getUser());
    }
    
    private function getCheckRecord(CompositionAction $action, $user)
    {   
        $response = false;
        
        switch ($action->getVoterData()) {
            case CompositionAction::TYPE_FAVOURITE :
                $response =  $action->hasFavourite() ? false : true;
            case CompositionAction::TYPE_SUBSCRIBTION :
                $record = $this->em->getRepository('AppBundle:CompositionAction')
                ->findOneBy(array('user' => $user, 'composition' => $action->getComposition(), 'subscribed' => true));
                break;
            default:
                throw new \Exception('Action Voter - data wrong parameter.');
        }

        return $response;
    }

}
