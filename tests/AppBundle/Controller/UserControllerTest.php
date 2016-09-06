<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of UserControllerTest
 *
 * @author dimas
 */
class UserControllerTest extends WebTestCase
{
    public function testLogInPage()
    {
        $client = self::createClient();
        $client->request('GET', '/ru/login');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function testRegisterPage()
    {
        $client = self::createClient();
        $client->request('GET', '/ru/register/');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
