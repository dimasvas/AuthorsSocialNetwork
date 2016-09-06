<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of SubscribtionControllerTest
 *
 * @author dimas
 */
class SubscribtionControllerTest extends WebTestCase 
{
    public function testPageAction()
    {
        $client = self::createClient();
        
        $client->request('GET', '/ru/subscriptions/page/1', array(), array(), array()
        );

        $this->assertTrue($client->getResponse()->isRedirect());
    }
    
    public function testIndexAction()
    {
        $client = self::createClient();
        
        $client->request('GET', '/ru/subscriptions/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
    
    public function testCreateAction()
    {
        $client = self::createClient();
        
        $client->request('POST', '/ru/subscriptions/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        //$this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
}
