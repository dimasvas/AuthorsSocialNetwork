<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of MessageControllerTest
 *
 * @author dimas
 */
class MessageControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = self::createClient();
        $client->request('GET', '/ru/message/list');

        $this->assertTrue($client->getResponse()->isRedirect());
    }
    
    public function testThreadsAction()
    {
        $client = self::createClient();
        
        $client->request('GET', '/ru/message/threads/2', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
    
    public function testCreateAction()
    {
        $client = self::createClient();
        
        $client->request('POST', '/ru/message/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
    
}
