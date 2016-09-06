<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of MainControllerTest
 *
 * @author dimas
 */
class MainControllerTest extends WebTestCase
{
    public function testIndexAction () 
    {
        $client = self::createClient();
        $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
