<?php

namespace AppBundle\Security;

use AppBundle\Entity\Composition;
use UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Description of CompositionVoter
 *
 * @author dimas
 */
class CompositionVoter extends Voter 
{
    const CREATE = 'create';
    const VIEW = 'view';
    const VIEW_TYPE = 'view_type';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const ANON_DEPENDENCY_VIEW = 'anon_dependency_view';
    const AUTH_DEPENDENCY_VIEW = 'auth_dependency_view';
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(
                self::VIEW, 
                self::VIEW_TYPE, 
                self::EDIT, 
                self::CREATE, 
                self::DELETE, 
                self::ANON_DEPENDENCY_VIEW, 
                self::AUTH_DEPENDENCY_VIEW))
            ) {
            
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Composition) {
            return false;
        }

        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        // you know $subject is a Post object, thanks to supports
        /** @var Composition composition */
        $composition = $subject;

        switch($attribute) {
            case self::VIEW:
                return $this->canView($composition, $user);
            case self::VIEW_TYPE:
                return $this->canViewType($composition, $user);    
            case self::ANON_DEPENDENCY_VIEW;
                return $this->canAnonDependencyView($composition);
            case self::AUTH_DEPENDENCY_VIEW;
                return $this->canAuthDependencyView($composition, $user);      
            case self::EDIT:
                return $this->canEdit($composition, $user);
            case self::CREATE:
                return $this->canCreate($user);
            case self::DELETE:
                return $this->canDelete($composition, $user);     
        }

        throw new \LogicException('This code should not be reached!');
    }
    
    private function canCreate($user) 
    {
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        if(!$user->isAuthor() || $user->isLocked()) {
            return false;
        }
        
        return true;
    }
    
    public function canDelete(Composition $composition, $user) 
    {
        // The user must be logged in; if not, deny access
        if (!$user instanceof User) {
            return false;
        }
        // The user must be the owner of the current composition
        if (!$user->isEqualTo($composition->getUser())) {
            return false;
        }
        // Composition must not be published
        if ($composition->getPublished()) {
            return false;
        }
        
        return true;
    }

    private function canView(Composition $composition, $user)
    {
        if($this->canEdit($composition, $user)) {
            return true;
        }
        
        if($composition->getBlocked()){
            return false;
        }

        return true;
    }
    
    private function canViewType(Composition $composition , $user)
    {        
        $allowedTypes = ['book', 'game', 'scenario', 'idea'];
        
        if(!in_array($composition->getTypeName(), $allowedTypes)) {
            return false;
        }
        
        if($this->canEdit($composition, $user)) {
            return true;
        }
        
        if($composition->getBlocked()){
            return false;
        }
        
        if(!$composition->getPublished()){
            return false;
        }
        
        return true;
    }

    private function canEdit(Composition $composition, $user)
    {
        if (!$user instanceof User) {
            return false;
        }
        
        return $user->isEqualTo($composition->getUser());
    }
    
    private function canAnonDependencyView($composition) 
    {
        if(!$composition->getPublished()){
            return false;
        }
        
        return true;
    }
    
    private function canAuthDependencyView($composition, $user) 
    {
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        if($composition->getBlocked()){
            return false;
        }
        
        return true;
    }

}
