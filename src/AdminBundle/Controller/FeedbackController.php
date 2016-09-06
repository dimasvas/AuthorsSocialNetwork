<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/feedback")
 * @Security("has_role('ROLE_ADMIN')");
 */
class FeedbackController extends Controller
{
    /**
     * Lists all Feedback entities.
     * 
     * @Route("/list/{page}", name="admin-feedback-list", defaults={"page" = 1})
     * @Method("GET")
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $queryBuilder = $em->getRepository('AppBundle:Feedback')->getFeedbacks();
        
        $pager = $this->get('pager')->getPagedResults($queryBuilder, $page, 20);
        
        return $this->render(
            'AdminBundle:Feedback:index.html.twig',
            array('pager' => $pager['pager'])
        );
    }
    
        /**
     * Finds and displays a Feedback entity.
     *
     * @Route("/item/{id}", name="admin-feedback-show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'AdminBundle:Feedback:show.html.twig',
            array(
                'entity'      => $entity,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }
    
        /**
     * Deletes a Feedback entity.
     *
     * @Route("/{id}", name="admin-feedback-delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Feedback')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feedback entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin-feedback-list'));
    }

    /**
     * Creates a form to delete a Feedback entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin-feedback-delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, array('label' => 'Удалить'))
            ->getForm()
        ;
    }
    
}
