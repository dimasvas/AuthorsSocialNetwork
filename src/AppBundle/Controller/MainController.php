<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller {
    /**
     * @Route("/{_locale}", 
     *      name="home_page", 
     *      defaults={"_locale": "ru"}, 
     *      requirements={"_locale": "ru|ua"},
     *      options={"expose"=true}
     * )
     */
    public function indexAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();

        $latest_composition = $em->getRepository('AppBundle:Composition')
                                    ->findBy(array(), array('created' => 'DESC'),  4);
        
        //TODO: Load by ajax -1 request
        $popular = $em->getRepository('AppBundle:Composition')
                                    ->findBy(array('archived' => false), array('total_rating_num' => 'DESC'),  4);
        
        return $this->render(
                    '@App/Page/index.html.twig',
                    array(
                        'latest' => $latest_composition,
                        'popular' => $popular
                    )
                );
    }
}
