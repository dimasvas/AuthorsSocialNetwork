<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="admin-dashboard")
     * @Security("has_role('ROLE_ADMIN')");
     */
    public function indexAction()
    {
        return $this->render(
            'AdminBundle:Dashboard:index.html.twig',
            array()
        );
    }
}
