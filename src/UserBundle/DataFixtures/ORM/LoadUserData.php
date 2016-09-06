<?php

namespace UserBundle\DataFixtures\ORM;

use AppBundle\Entity\Author;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $data =  $this->getInitData();

        //$data = $this->getProdData();
        
        foreach($data as $item){

            $entity = new User();

            $entity->setEmail($item['email']);
            $entity->setUsername($item['username']);
            $entity->setPlainPassword($item['password']);

            $entity->setName($item['name']);
            $entity->setSurname($item['surname']);
            $entity->setMiddleName($item['middleName']);
            $entity->setGender($item['gender']);
            $entity->setDateOfBirth($item['dateOfBirth']);
            $entity->setHideYearOfBirth($item['hideYearOfBirth']);
            $entity->setCountry($item['country']);
            $entity->setCity($item['city']);

            $entity->setFriendship(20);
            $entity->setIsAuthor($item['isAuthor']);
            $entity->setEnabled(true);
            $entity->addRole($item['role']);

            if ($item['role'] == 'ROLE_SUPERADMIN') {
                $entity->setSuperAdmin(true);
            }


            if ($item['role'] == 'ROLE_AUTHOR') {
                $entity->setAuthor($this->createAuthor($manager));
            }

            $manager->persist($entity);
            $manager->flush();
        }
        
        $this->loadMore($manager);
    }
    
    private function loadMore ($manager) {
        
        $item = $this->getInitAuthorData();
        
        for($i = 1; $i <= 20; $i++) {
           $entity = new User();

            $entity->setEmail($i . $item['email']);
            $entity->setUsername($item['username']. $i);
            $entity->setPlainPassword($item['password']);

            $entity->setName($item['name'] .$i);
            $entity->setSurname($item['surname'] .$i);
            $entity->setMiddleName($item['middleName'] .$i);
            $entity->setGender($item['gender']);
            $entity->setDateOfBirth($item['dateOfBirth']);
            $entity->setHideYearOfBirth($item['hideYearOfBirth']);
            $entity->setCountry($item['country']);
            $entity->setCity($item['city']);

            $entity->setIsAuthor($item['isAuthor']);
            $entity->setEnabled(true);
            $entity->addRole($item['role']);

            if ($item['role'] == 'ROLE_SUPERADMIN') {
                $entity->setSuperAdmin(true);
            }


            if ($item['role'] == 'ROLE_AUTHOR') {
                $entity->setAuthor($this->createAuthor($manager));
            }

            $manager->persist($entity);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    private function createAuthor($manager)
    {
        $author = new Author();

        $manager->persist($author);
        $manager->flush();

        return $author;
    }
    
    private function getProdData() 
    {
        return [
                [
                'email' => 'dmv.developer@gmail.com',
                'username' => 'admin_good',
                'password' => 'password',

                'name' => 'Добрый',
                'surname' => 'Администратор',
                'middleName' => '',
                'gender' => 'male',
                'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
                'hideYearOfBirth' => true,
                'country' => 'UA',
                'city' => 'Одесса',
                'isAuthor' => 'no',
                'role' => 'ROLE_SUPERADMIN'
            ],
             [
                'email' => 'jemmi777@gmail.com',
                'username' => 'admin_positive',
                'password' => 'password',

                'name' => 'Позитивный',
                'surname' => 'Администратор',
                'middleName' => '',
                'gender' => 'female',
                'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
                'hideYearOfBirth' => true,
                'country' => 'UA',
                'city' => 'Одесса',
                'isAuthor' => 'no',
                'role' => 'ROLE_AMDIN'
            ]
        ];
    }
    
    private function getInitAuthorData () 
    {
        return [
            'email' => '_author@author.com',
            'username' => 'author_',
            'password' => '123456',

            'name' => 'Имя_',
            'surname' => 'Фамилия_',
            'middleName' => 'Отчество_',
            'gender' => 'male',
            'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
            'hideYearOfBirth' => true,
            'country' => 'UA',
            'city' => 'Odessa',
            'isAuthor' => 'yes',
            'role' => 'ROLE_AUTHOR'
        ];
    }

    private function getInitData()
    {
        return [
            [
                'email' => 'test@test.com',
                'username' => 'Test',
                'password' => '123456',

                'name' => 'La-testo',
                'surname' => 'Author',
                'middleName' => 'Qutestovich',
                'gender' => 'male',
                'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
                'hideYearOfBirth' => false,
                'country' => 'UA',
                'city' => 'Odessa',
                'isAuthor' => 'yes',
                'role' => 'ROLE_AUTHOR'
            ],
            [
                'email' => 'user@user.com',
                'username' => 'User',
                'password' => '123456',

                'name' => 'Андрей',
                'surname' => 'Коваль',
                'middleName' => 'Николаевич',
                'gender' => 'male',
                'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
                'hideYearOfBirth' => false,
                'country' => 'UA',
                'city' => 'Odessa',
                'isAuthor' => 'no',
                'role' => 'ROLE_USER'
            ],
            
            [
                'email' => 'superadmin@admin.com',
                'username' => 'superadmin',
                'password' => '123456',

                'name' => 'SuperadminИмя',
                'surname' => 'SuperAdminФамилия',
                'middleName' => 'SuperadminОтчество',
                'gender' => 'male',
                'dateOfBirth' => new \DateTime(date('Y-m-d H:i:s')),
                'hideYearOfBirth' => true,
                'country' => 'UA',
                'city' => 'Odessa',
                'isAuthor' => 'no',
                'role' => 'ROLE_SUPERADMIN'
            ]
        ];
    }
}