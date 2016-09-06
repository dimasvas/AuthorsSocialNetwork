<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Composition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\CompositionCategory;

/**
 * Composition controller.
 *
 * @Route("/{_locale}/composition", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"})
 */
class CompositionController extends Controller
{
     /**
     * Finds and displays a Composition entity.
     *
     * @Route("/{id}", name="composition_show", options={"expose"=true})
     * @Method("GET")
     */
    public function showAction($id)
    {       
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppBundle:Composition')
                                        ->getWithCategoryGenres($id);
        
        if(!$entity) {
            throw new NotFoundHttpException('Composition not found');
        }
                
        $this->denyAccessUnlessGranted('view', $entity);
        
        if($this->getUser()) {
            $rating = $em->getRepository('AppBundle:Rating')
                ->findOneBy(['user' => $this->getUser(), 'composition' => $entity]);
        
            $favouriteRecord = $em->getRepository('AppBundle:FavouriteComposition')
                                        ->findOneBy(['user' => $this->getUser(), 'composition' => $entity]);

            $subscribtionRecord = $em->getRepository('AppBundle:CompositionSubscription')
                                    ->findOneBy(['user' => $this->getUser(), 'composition' => $entity]);

            $isOwnerAuthor = $this->get('app.user_checker')->isOwnerAuthor($entity->getUser());

            if($isOwnerAuthor) {
                $genres = $entity->getCategory()->getGenres()->map(
                        function($obj) {
                            return array(
                                'value' => $obj->getId(),
                                'text' => $obj->getName()
                            );
                        });

                $genresIds = $entity->getGenres()->map(
                        function($obj) {
                            return $obj->getId();

                        });
            }

            $deleteForm = $this->createArchiveForm($entity);
            
            return $this->render(
                'AppBundle:Composition:show.html.twig',
                array(
                        'entity'      => $entity,
                        'user_rate' => $rating ? $rating->getRate() : 0,
                        'is_subscribed' => $subscribtionRecord ? true : false,
                        'favourite' => $favouriteRecord ? $favouriteRecord : false,
                        'isOwnerAuthor' => $isOwnerAuthor,
                        'entityVisible' => $entity->getPublished() ? 'true' : 'false',
                        'all_genres' =>  $isOwnerAuthor ? json_encode($genres->getValues()) : '',
                        'selected_genres' => $isOwnerAuthor ? json_encode($genresIds->getValues()): '',
                        'delete_form' => $deleteForm->createView(),
                        'pagedTypes' => ['book', 'game', 'scenario', 'idea'],
                        'textTypes' => ['book', 'game', 'scenario', 'idea', 'lyric', 'verses'],
                        'videoTypes' => ['video', 'animation']
                )
            );
        }

        return $this->render(
            'AppBundle:Composition:show.html.twig',
            array(
                    'entity'      => $entity,
                    'entityVisible' => $entity->getPublished() ? 'true' : 'false',
                    'isOwnerAuthor' => false,
                    'pagedTypes' => ['book', 'game', 'scenario', 'idea'],
                    'textTypes' => ['book', 'game', 'scenario', 'idea', 'lyric', 'verses'],
                    'videoTypes' => ['video', 'animation']
            )
        );
    }
    
    /**
     * Finds and displays a Composition entity.
     *
     * @Route("/show/{id}/{page}", 
     *      name="composition_type_show", 
     *      defaults={"page" = 1},
     *      requirements={"page": "\d+"}  
     * )
     * @Method("GET")
     * @ParamConverter("user", class="AppBundle:Composition", options={
     *    "repository_method" = "getWithCategoryGenres",
     *    "mapping": {"id": "id"},
     *    "map_method_signature" = true
     * })
     */
    public function showTypeAction(Composition $composition, $page)
    {   
        $this->denyAccessUnlessGranted('view_type', $composition);
        //TODO: Redo with join
        $type = $composition->getType();
        
        $processor = $this->get('app.text_processor');

        try {
            $text = $processor->getPagedData($type, $page);
        } catch (NotFoundHttpException $e) {
            throw $e;
        }
        
        return $this->render(
            'AppBundle:CompositionTypes:text_show.html.twig',
            array(
                'composition' => $composition,
                'entity' => $type,
                'text' => $text,
                'currentPage' => $page
            )
        );
    }

    /**
     * Displays a form to create a new Composition entity.
     *
     * @param Request $request
     * @return array
     *
     * @Route("/new", name="composition_new")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function newAction(Request $request)
    {
        $entity = new Composition();
        
        $this->denyAccessUnlessGranted('create', $entity);
   
        $category = $this->getTypeFromRequest($request);
        
        try {
            $form  = $this->createCreateForm($entity, $category);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $this->render(
            'AppBundle:Composition:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'type' =>  $category->getAlias()
            )
        );
    }
    
    
    /**
     * Creates a new Composition entity.
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/", name="composition_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')");
     */
    public function createAction(Request $request)
    {
        $entity = new Composition();
        
        $this->denyAccessUnlessGranted('create', $entity);
        
        //TODO: bad logic (maybe). Refactor 
        $category = $this->getTypeFromRequest($request);
        $form = $this->createCreateForm($entity, $category);
        
        $form->handleRequest($request);
        
        $form->getData()
                ->setAuthor($this->getUser()->getAuthor())
                ->setUser($this->getUser())
                ->setCategory($category)
                ->setTypeName($category->getAlias())
                ->setStatusInProcess()
                ->setType($this->getTypeInstance($category));
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($form->getData());
            $em->persist($this->getUser()->getAuthor()->incrementTotalCompositions());  
            $em->flush();
            
            return $this->redirect(
                $this->generateUrl('composition_show', array('id' => $entity->getId()))
            );
        }

         return $this->render(
            'AppBundle:Composition:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView()
            )
        );
    }
    
    /**
     * @param string $type
     * 
     * @Route("/update/{id}", 
     *      name="composition_update", 
     *      condition="request.isXmlHttpRequest()", 
     *      options={"expose"=true}
     * )
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')");
     */
    public function updateAction(Request $request, Composition $entity)
    {   
        $this->denyAccessUnlessGranted('edit', $entity);
        
        $type = $request->request->get('name');
        $value = $request->request->get('value');
        
        $em = $this->getDoctrine()->getManager();
        
        switch ($type){
            case 'title':
                $entity->setTitle($value);
                break;
            case 'description':
                $entity->setDescription($value);
                break;
            case 'language':
                $entity->setLanguage($value);
                break;
            case 'genres':
                $genres = $em->getRepository('AppBundle:Genre')->findBy(['id' => $value]);
                
                if(!$genres){
                    throw new \Exception('Wrong genres values.', 400);
                }
                
                //check if ids exists in type
                $allgenres = $entity->getCategory()->getGenres();
                
                foreach( $genres as $genre){
                    if(!$allgenres->contains($genre)){
                         throw new \Exception('No such genre in the type.', 400);
                    }
                }
        
                $entity->clearGenres();

                foreach($genres as $genre){
                    $entity->addGenre($genre);
                }
                    
                break;
            case 'status':
                $entity->setStatus($value);
                break;
            case 'published':
                $entity->setPublished($value);
                break;
            case 'video_content':
                if(!in_array($entity->getTypeName() , array('video', 'animation'))) {
                    throw new \Exception('Wrong video content.', 400);
                }
                
                $entity->getType()->setContent($value);
                break;
            default:
                throw new \Exception('Non valid parameter.');
            
        }
        
        $validator = $this->get('validator');
        $errors = $validator->validate($entity);
        
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            
            return new JsonResponse(['message' => $errorsString], 400);
        }
        
        $em->persist($entity);
        $em->flush();
        
        return new JsonResponse(['status' => 'success']);
    }
    
     
    /**
     * Displays a form to edit an existing Composition Type entity.
     *
     * @Route("/edit/content/{id}", 
     *      name="composition_edit_content"
     * )
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')");
     */
    public function editTypeAction(Request $request, Composition $composition)
    { 
        $this->denyAccessUnlessGranted('edit', $composition);
        
        $category = $composition->getCategory();
        
        $form = $this->createForm($this->getTypeFormClass($category), $composition->getType());
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->handleEditType($form, $category);
            
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $pagedTypes = ['book', 'game', 'scenario', 'idea'];
        
            if(in_array($composition->getTypeName(), $pagedTypes)) {
                return $this->redirectToRoute('composition_type_show', array('id' => $composition->getId()));
            }else {
                return $this->redirectToRoute('composition_show', array('id' => $composition->getId()));
            }
        }
        
        return $this->render(
            'AppBundle:CompositionTypes:book_edit.html.twig',
            array(
                'form' => $form->createView(),
                'composition' => $composition
            )
        );
    }
    
    private function handleEditType($form, $category) 
    {
        $processor = $this->get('app.text_processor');
            
        switch($category->getAlias()){
            case 'book':
            case 'game':
            case 'scenario':
            case 'idea':
                $text = $processor->processText($form->getData()->getContent());
                $form->getData()->setContent($text);
                $form->getData()->setPages($processor->countPage($text));
                break;
            case 'lyric':
            case 'verses':
                $text = $processor->processText($form->getData()->getContent());
                $form->getData()->setContent($text);
               break;
            case 'music':
            case 'video':
            case 'animation':
                break;
            default:
                throw new \Exception('Wrong category type');
        }
    }
    
    /**
     * Deletes a Composition entity.
     *
     * @Route("/delete/{id}", name="composition_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')");
     */
    public function deleteAction(Request $request, Composition $composition)
    {
        $this->denyAccessUnlessGranted('delete', $composition);
        
        $form = $this->createDeleteForm($composition);
        $form->handleRequest($request);
        
        $user = $composition->getUser();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($composition);
            $em->flush();
            
            return $this->redirect($this->generateUrl('author_show', ['id' => $user->getId()]));
        }
        
        return new \Exception('Unable to delete entity');
    }
    /**
     * Deletes a Composition entity.
     *
     * @Route("/archive/{id}", name="composition_archive")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')");
     */
    public function archiveAction(Request $request, Composition $composition)
    {           
        $this->denyAccessUnlessGranted('edit', $composition);
            
        $form = $this->createArchiveForm($composition);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
                        
            $isRemovable = $this->isRemovable($composition);
            
            if($isRemovable) { 
                $composition->clearAttachments();
                $em->remove($composition);
                $em->flush();
                
                $authorObj = $composition->getUser()->getAuthor();
                $authorObj->decreaseTotalCompositions();
                $em->persist($authorObj);
                $em->flush();
    
                return $this->redirect($this->generateUrl('author_show',array('id' => $this->getUser()->getId())));
            } else {
                $composition->setArchived(true)
                            ->setStatus(Composition::STATUS_DELETED)
                            ->clearAttachments();

                $type = $composition->getType();

                $this->get('app.archive_compositon')->processContent($type);

                $em->persist($type);
                $em->persist($composition);
                $em->flush();
                
                return $this->redirect($this->generateUrl('composition_show',array('id' => $composition->getId())));
            }
        }
    }
    
    private function isRemovable($composition) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $hasFavourite = $em->getRepository('AppBundle:FavouriteComposition')->findOneBy(['composition' => $composition]);
        
        if($hasFavourite) {
            return false;
        }
        
        $hasSubscribtion = $em->getRepository('AppBundle:CompositionSubscription')->findOneBy(['composition' => $composition]);
        
        if($hasSubscribtion) {
            return false;
        }
        
        $hasComment = $em->getRepository('AppBundle:Comment')->findOneBy(['composition' => $composition]);
        
        if($hasComment) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Creates a form to create a Composition entity.
     *
     * @param Composition $entity The entity
     * @param CompositionEntityType $type The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Composition $entity, CompositionCategory $category)
    {
        $form = $this->createForm('AppBundle\Form\CompositionType', $entity, array(
            'action' => $this->generateUrl('composition_create'),
            'composition_category' =>  $category
        ));

        return $form;
    }
    
    /**
     * Creates a form to delete a Composition entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createArchiveForm($composition)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('composition_archive', array('id' => $composition->getId())))
            ->setMethod('DELETE')
             ->add('submit', SubmitType::class, 
                    array(
                        'label' => 'common.delete', 
                        'attr' => array('class' =>'btn-default btn-sm')
                    )
                )
            ->getForm()
        ;
    }
    
        /**
     * Creates a form to delete a Composition entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Composition $composition)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('composition_delete', array('id' => $composition->getId())))
            ->setMethod('DELETE')
            ->add('submit', SubmitType::class, 
                    array(
                        'label' => 'common.delete', 
                        'attr' => array('class' =>'btn-default btn-sm')
                    )
                )
            ->getForm()
        ;
    }
    
     /**
     * @param $request
     * @return CompositionEntityType
     */
    private function getTypeFromRequest($request)
    {
        //TODO: check crsf token/ Enable Native Entity Validation
        $categoryId = $request->request->get('composition_category_select')['id'] ?
                $request->request->get('composition_category_select')['id']  :
                $request->request->get('composition')['category_id'];
        
        if(!$categoryId) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:CompositionCategory')->find($categoryId);

        if (!$category) {
            throw new \Exception("Unable to find CompositionCategory entity.");
        }

        return $category;
    }
    
    /**
     * 
     * @param type $category
     * @return \AppBundle\Controller\typeClass
     * @throws \Exception
     */
    private function getTypeInstance($category)
    {
        $typeClass = 'AppBundle\Entity\CompositionTypes\\' . ucfirst($category->getAlias());
            
        if(!class_exists($typeClass)) {
            throw new \Exception('Class Type violation. The class does not exists: ' . $typeClass);
        }
            
        return new $typeClass(); 
        
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($type);
//        $em->flush();
//        
//        return $type;
    }
    
    /**
     * 
     * @param type $category
     * @return type
     */
    private function getTypeFormClass($category) 
    {
        return 'AppBundle\Form\CompositionTypes\\' . ucfirst($category->getAlias()) . 'Type';
    }

}
