<?php

namespace Tests\AppBundle\From\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use UserBundle\Form\Type\RegistrationFormType;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Validation;

/**
 * Description of RegisterTypeTest
 *
 * @author dimas
 */
class RegisterTypeTest extends TypeTestCase
{
     /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $user = new User();
        
        $user->setAboutMe($data['aboutMe'])
             ->setAliasName($data['aliasName'])
             ->setCity($data['city'])
             ->setCountry($data['country'])
             ->setEmail($data['email'])
             ->setName($data['name'])
             ->setSurname($data['surname'])  
             ->setMiddleName($data['middleName'])
             ->setPassword($data['password'])
             ->setAliasName($data['aliasName'])
             ->setShowAliasName($data['showAliasName'])
             ->setGender($data['gender'])
             ->setHideYearOfBirth($data['hideYearOfBirth'])
             ->setIsAuthor($data['isAuthor'])
             ->setLocale($data['locale']);   
        
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        
        $errors = $validator->validate($user);

        $this->assertEquals(0, count($errors));
                
    }
    
    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'name' => 'Name',
                    'surname' => 'TestSurname',
                    'password' => '123456',
                    'middleName' => 'MiddleNameTest',
                    'aliasName' => 'AliasNameTest',
                    'showAliasName' => 0,
                    'hideYearOfBirth' => 1,
                    'gender' => 'male',
                    'locale' => 'rssssu',
                    'country' => 'Ukraine',
                    'city' => 'Odessa',
                    'aboutMe' => 'AboutMe',
                    'email' => 'email@email.com',
                    'isAuthor' => 1
                ),
            )
        );
    }
}
