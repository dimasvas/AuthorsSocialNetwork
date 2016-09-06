<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of UpdateControllerTest
 *
 * @author dimas
 */
class UpdateControllerTest extends WebTestCase 
{
    public function testIndexAction()
    {
        $client = self::createClient();
        
        $client->request('GET', '/composition-update/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
}
