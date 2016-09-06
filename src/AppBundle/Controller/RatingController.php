<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Rating;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Composition;

/**
 * Rating controller.
 *
 * @Route("/rating")
 * @Security("has_role('ROLE_USER')");
 */
class RatingController extends Controller
{
    /**
     * Creates a new Rating entity.
     *
     * @Route("/{id}", 
     *      name="rating_create",
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true})
     * )
     * @Method("POST")
     */
    public function createAction(Request $request, Composition $composition)
    {
        $rate = $request->request->get('rate');

        if(!$rate) {
            throw new HttpException('400', 'Parameter not found Exception');
        }
        
        $this->denyAccessUnlessGranted('auth_dependency_view', $composition);
        
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppBundle:Rating')
                ->findOneBy(['user' => $this->getUser(), 'composition' => $composition]);
        
        if(!$entity) {
            $entity = new Rating();
        }
        
        $entity->setComposition($composition)
                ->setRate($rate)
                ->setUser($this->getUser());
        
        $em->persist($entity);
        $em->flush();
        
        $rate_data = $this->saveStatistics($composition);
        
        return new JsonResponse([
            'status' => 'success',
            'rate' => $rate,
            'rate_data' => $rate_data
        ]);
    }
    
    private function saveStatistics($composition)
    {
        $rate = $this->getRate($composition);
        
        if(!$rate) {
            throw new \Exception('No rates in table. Logical error.');
        }
        
        $composition->setTotalRatingNum($rate['total_rating']);
        $composition->setNumUsersRate($rate['hits']);
        
        $em = $this->getDoctrine()->getManager();
        
        $em->persist($composition);
        $em->flush();
        
        return $rate;
    }        
    /**
     * 
     * @param type $composition
     */
    private function getRate($composition)
    {
        $em = $this->getDoctrine()->getManager();
        
        $rate = $em->getRepository('AppBundle:Rating')->getCompositionStat($composition);
        
        $rate['total_rating'] = round($rate['total_rating'], 2);
        
        return $rate;
    }
}
