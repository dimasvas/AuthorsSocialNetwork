<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of VoteControllerTest
 *
 * @author dimas
 */
class VoteControllerTest extends WebTestCase
{
    public function testCreateAction()
    {
        $client = self::createClient();
        
        $client->request('POST', '/vote/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
}
