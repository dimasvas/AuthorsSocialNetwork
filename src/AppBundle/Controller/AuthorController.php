<?php

namespace AppBundle\Controller;

use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Author;

/**
 * Class AuthorController
 * @package AppBundle\Controller
 *
 * @Route("/{_locale}/author", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 */
class AuthorController extends Controller
{
    /**
     * @param int $page
     * @return array
     *
     * @Route("/list/{page}", name="authors_list", defaults={"page" = 1})
     * @Method({"GET"})
     */
    public function listAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        
        $queryBuilder = $em->getRepository('AppBundle:Author')->getAuthors();

        $paginator = $this->get('app.pagination')
                        ->getORMResults($queryBuilder, $page, Author::DEFAULT_NUM_ITEMS);
        
        return $this->render('AppBundle:Author:list.html.twig',
            array(
                'collection' => $paginator->getCurrentPageResults(),
                'pager' => $paginator
            )
        );
    }

    /**
     * @Route("/{id}", name="author_show", options={"expose"=true})
     * @Method({"GET"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $authorUser = $em->getRepository('AppBundle:Author')->getAuthor($id);
        } catch (NoResultException $e) {
            throw new \Exception('No such author');
        }
        
        $form = $this->createForm('AppBundle\Form\CompositionCategorySelectType', null, array(
            'action' => $this->generateUrl('composition_new'),
        ));
        
        $isOwnerAuthor = $this->get('app.user_checker')->isOwnerAuthor($authorUser);
        
        $compositions = $em->getRepository('AppBundle:Composition')->getAllAuthors($authorUser);
        
        return $this->render('AppBundle:Author:show.html.twig',
            array(
                'entity' => $authorUser,
                'compositions' => $compositions,
                'form' => $form->createView(),
                'isOwnerAuthor' => $isOwnerAuthor
            )
        );
    }
}