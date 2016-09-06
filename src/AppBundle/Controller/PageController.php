<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/{_locale}/page", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 */
class PageController extends Controller
{
    /**
     * @Route("/contacts", name="contacts_page")
     */
    public function contactsAction()
    {
        $provider = $this->get('security.csrf.token_manager');
        $_token = $provider->refreshToken('unknown');
        
        return $this->render('@App/Page/contacts.html.twig', array('_token' => $_token));
    }

    /**
     * @Route("/help", name="help_page")
     */
    public function helpAction()
    {
        return $this->render('@App/Page/help.html.twig');
    }

    /**
     * @Route("/about-us", name="about_us_page")
     */
    public function aboutUsAction()
    {
        return $this->render('@App/Page/about_us.html.twig');
    }

    /**
     * @Route("/donate", name="donate_page")
     */
    public function donateAction()
    {
        return $this->render('@App/Page/donate.html.twig');
    }
    
    /**
     * @Route("/rules", name="rules_page")
     */
    public function rulesAction()
    {
        return $this->render('@App/Page/rules.html.twig');
    }
}
