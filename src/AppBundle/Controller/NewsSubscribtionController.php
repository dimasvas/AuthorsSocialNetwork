<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\NewsSubscribtion;
use AppBundle\Form\NewsSubscribtionType;

/**
 * NewsSubscribtion controller.
 *
 * @Route("/news-subscribtion")
 */
class NewsSubscribtionController extends Controller
{
    /**
     * Lists all NewsSubscribtion entities.
     *
     * @Route("/", name="news-subscribtion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $newsSubscribtions = $em->getRepository('AppBundle:NewsSubscribtion')->findAll();

        return $this->render('newssubscribtion/index.html.twig', array(
            'newsSubscribtions' => $newsSubscribtions,
        ));
    }

    /**
     * Creates a new NewsSubscribtion entity.
     *
     * @Route("/new", 
     *      name="news_subscribtion_new",
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true})
     * )
     * @Method({"POST"})
     */
    public function newAction(Request $request)
    {
        $newsSubscribtion = new NewsSubscribtion();
        $form = $this->createForm('AppBundle\Form\NewsSubscribtionType', $newsSubscribtion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsSubscribtion);
            $em->flush();

            return $this->redirectToRoute('news-subscribtion_show', array('id' => $newsSubscribtion->getId()));
        }

        return $this->render('newssubscribtion/new.html.twig', array(
            'newsSubscribtion' => $newsSubscribtion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a NewsSubscribtion entity.
     *
     * @Route("/{id}", name="news-subscribtion_show")
     * @Method("GET")
     */
    public function showAction(NewsSubscribtion $newsSubscribtion)
    {
        $deleteForm = $this->createDeleteForm($newsSubscribtion);

        return $this->render('newssubscribtion/show.html.twig', array(
            'newsSubscribtion' => $newsSubscribtion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NewsSubscribtion entity.
     *
     * @Route("/{id}/edit", name="news-subscribtion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, NewsSubscribtion $newsSubscribtion)
    {
        $deleteForm = $this->createDeleteForm($newsSubscribtion);
        $editForm = $this->createForm('AppBundle\Form\NewsSubscribtionType', $newsSubscribtion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsSubscribtion);
            $em->flush();

            return $this->redirectToRoute('news-subscribtion_edit', array('id' => $newsSubscribtion->getId()));
        }

        return $this->render('newssubscribtion/edit.html.twig', array(
            'newsSubscribtion' => $newsSubscribtion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a NewsSubscribtion entity.
     *
     * @Route("/{id}", name="news-subscribtion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, NewsSubscribtion $newsSubscribtion)
    {
        $form = $this->createDeleteForm($newsSubscribtion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($newsSubscribtion);
            $em->flush();
        }

        return $this->redirectToRoute('news-subscribtion_index');
    }

    /**
     * Creates a form to delete a NewsSubscribtion entity.
     *
     * @param NewsSubscribtion $newsSubscribtion The NewsSubscribtion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NewsSubscribtion $newsSubscribtion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('news-subscribtion_delete', array('id' => $newsSubscribtion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
