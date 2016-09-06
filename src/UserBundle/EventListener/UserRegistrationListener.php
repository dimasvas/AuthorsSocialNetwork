<?php

namespace UserBundle\EventListener;

use AppBundle\Entity\Author;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserRegistrationListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     *
     * @var type 
     */
    private $router;
    
    private $secret = '6LcCih0TAAAAAGh_IeZcLos25G4z_q-v1tskJoSS';
    
    private $devSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
    
    private $prodDomain = 'natuchke.org';

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, UrlGeneratorInterface $router)
    {
        $this->em = $entityManager;
        $this->router = $router;
    }
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED  => 'onRegistrationCompleted',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        
        if ($user->isAuthor()) {
            $author = new Author();

            $this->em->persist($author);
            $this->em->flush();

            $user->setAuthor($author);
            $user->addRole('ROLE_AUTHOR');
        } else {
             $user->addRole('ROLE_USER');
        }
        

        $this->em->persist($user);
        $this->em->flush();

    }
    
    public function onRegistrationSuccess (FormEvent $event)
    {
        $remoteIp = $event->getRequest()->getClientIp();
        
        $gRecaptchaResponse = $event->getRequest()->get('g-recaptcha-response', null);
       
        if($gRecaptchaResponse == '') {
            throw new \Exception('Captcha error', 400);
        }
        
        $secret = ($event->getRequest()->getHost() == $this->prodDomain) ? $this->secret : $this->devSecret;
        
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        
        $resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
        
        if (!$resp->isSuccess()) {
            throw new \Exception('Captcha error', 400);
        }
    }

}