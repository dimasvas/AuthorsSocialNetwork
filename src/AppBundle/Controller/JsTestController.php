<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class JsTestController extends Controller
{

    public function testCommonAction () 
    {
        return $this->render('AppBundle:JsTest:common.html.twig',array());
    }
}
