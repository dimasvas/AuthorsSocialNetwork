<?php

namespace UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ProfileEditListener implements EventSubscriberInterface
{
    /**
     * @var
     */
    private $token;

    /**
     * @var
     */
    private $em;

    /**
     * @param $em
     * @param $securityContext
     */
    public function __construct($em, $tokenStorage) {
        $this->token = $tokenStorage->getToken();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_SUCCESS  => 'onProfileEditSuccess',
        );
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        $submittedUser = $event->getForm()->getData();
        $dbUser = $this->token->getUser();

        /**
         * Prevent Modifying persistent User Data.
         */
        $submittedUser->setName($dbUser->getName());
        $submittedUser->setSurname($dbUser->getSurname());
        $submittedUser->setUsername($dbUser->getUsername());
        $submittedUser->setDateOfBirth($dbUser->getDateOfBirth());

        /**
         * If AliasName is Empty Uncheck ShowAliasName checkbox
         */
        if($submittedUser->getAliasName() == null) {
            $submittedUser->setShowAliasName(false);
        }

    }
}