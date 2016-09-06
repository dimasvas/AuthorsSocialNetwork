<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Intl\Intl;

/**
 * @Route("/user")
 */
class MainController extends Controller
{
    /**
     * 
     * @param type $id
     * 
     * @Route("/profile/show/{id}", 
     *      name="profile_show",
     *      options={"expose"=true}
     * )
     * @Method({"GET"})
     */
    public function showAction($id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        if($entity->isAuthor() == true){
            return $this->redirect($this->generateUrl('author_show', array('id' => $entity->getId())));
        }

        return $this->render('UserBundle:Profile:show.html.twig', array(
                    'user' => $entity,
        ));
    }
    
    /**
     * 
     * @param type $name
     * 
     * @Route("/check-nickname", name="user_check_nickname", options={"expose"=true})
     * @Method({"POST"})
     */
    public function checkNickNameAction(Request $request){
        
        $username = $request->request->get('username');
        
        if(!$username){
            throw new Exception("Empty username is given.");
        }
        
        if($this->isAdminUsername($username)) {
            return new JsonResponse(false);
        }

        $em = $this->getDoctrine()->getManager(); 
        $entity = $em->getRepository('UserBundle:User')->isUsernameExists($username);   

        return new JsonResponse(!$entity ? true: false);
    }
    
    /**
     * 
     * @param type $id
     * 
     * @Route("/{_locale}/settings", defaults={"_locale": "ru"}, requirements={"_locale": "ru|ua"}, name="user-settings")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')");
     */
    public function passwordSucceededAction(Request $request)
    {
        $countries = Intl::getRegionBundle()->getCountryNames($this->getUser()->getLocale());
        
        if($request->getLocale() === 'ua') {
            $countries = Intl::getRegionBundle()->getCountryNames('uk');
        }
        
        return $this->render('UserBundle:Profile:settings.html.twig', array(
                'user' => $this->getUser(),
                'countries' => json_encode($countries)
        ));
    }
    
    /**
     * 
     * @param string $type
     * 
     * @Route("/profile/edit", name="user_edit_profile", options={"expose"=true})
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')");
     */
    public function editProfileAction(Request $request)
    {
        $type = $request->request->get('name');
        $value = $request->request->get('value');
        
        $user = $this->getUser();
               
        switch ($type){
            case "show_alias":
                $user->setShowAliasName($value);
                break;
            case "hide_birthday":
                $user->setHideYearOfBirth($value);
                break;
            case "about_me":
                $user->setAboutMe($value);
                break;
            case "edit_city":
                $user->setCity($value);
                break;
            case "edit_country":
                $user->setCountry($value);
                break;
            case "edit_locale":
                if(!$this->get('app.locale')->isLocaleValid($value)){ //remove from service
                    throw new \Exception('Please provide a valid Locale value.');
                }
                $user->setLocale($value);
                break;
            default:
                throw new \Exception('Please provide a valid parameter.');
        }
        
        $validator = $this->get('validator', array('Profile'));
        $errors = $validator->validate($user);
        
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            
            return new JsonResponse(['message' => $errorsString], 400);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        return new JsonResponse(['message' => 'success']);
    }
    
    /**
     * 
     * @param string $type
     * 
     * @Route("/account/edit/email", name="user_edit_email", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_USER')");
     */
    public function editEmail(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        
        $user = $this->getUser();
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        
        $passwordIsValid = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
        
        if(!$passwordIsValid) {
            return new JsonResponse(['error' => true, 'message' => 'Wrong Password']);
        }
        
        $em = $this->getDoctrine()->getManager();
        $validator = $this->get('validator', array('Profile'));
                
        $user->setEmail($email);
        $errors = $validator->validate($user);
        //TODO: Email validation not working
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            
            return new JsonResponse(['error' => true, 'message' => $errorsString]);
        }
        
        $em->persist($user);
        $em->flush();
        
        return new JsonResponse(['message' => 'success']);
    }
    
    /**
     * 
     * @param string $type
     * 
     * @Route("/account/edit/password", name="user_edit_password", options={"expose"=true})
     * @Method({"PUT"})
     * @Security("has_role('ROLE_USER')");
     */
    public function editPassword(Request $request)
    {
        $password = $request->request->get('password');
        $newPassword = $request->request->get('newPassword');
        $retypePassword = $request->request->get('retypePassword');
        $response = ['message' => 'success'];
        
        $user = $this->getUser();
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $validator = $this->get('validator', array('Settings'));
        
        $passwordIsValid = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
        
        if(!$passwordIsValid) {
            $response = ['error' => true, 'message' => 'Wrong Password'];
        }
       
        if($newPassword !== $retypePassword){
            $response = ['error' => true, 'message' => 'Different new passwords'];
        }
        
        //TODO: Password length validation not working
        $user->setPlainPassword($newPassword);
        $errors = $validator->validate($user);
        
        if (count($errors) > 0) {
            $response = ['error' => true, 'message' => (string) $errors];
        }
        
        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();
        
        return new JsonResponse($response);
    }
    
    
    private function isAdminUsername($username)
    {
        if (strpos($username, 'admin') !== false ) {
           return true;
        }
        
        return false;
    }
}
