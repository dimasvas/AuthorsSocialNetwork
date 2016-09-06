<?php

namespace AppBundle\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

/**
 * Description of AuthorChecker
 *
 * @author dimas
 */
class UserChecker {
    
    protected $authorizationChecker;
    protected $token;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->token = $tokenStorage->getToken();
    }
    
    public function isOwnerAuthor(User $user) {
        
        $isAuthor = false;
        
        if (true === $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $isAuthor = $this->token->getUser()->isEqualTo($user);
        }
        
        return $isAuthor;
    }
}
