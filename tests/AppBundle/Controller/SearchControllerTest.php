<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of SearchControllerTest
 *
 * @author dimas
 */
class SearchControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = self::createClient();
        $client->request('GET', '/ru/search/page');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
