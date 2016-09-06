<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of AttachmentControllerTest
 *
 * @author dimas
 * 
 * http://stackoverflow.com/questions/9400233/testing-ajax-xmlhttprequest-pages-functionally-in-symfony2
 */
class AttachmentControllerTest extends WebTestCase
{
    public function testDeleteAction()
    {
        $client = self::createClient();
        
        $client->request('DELETE', '/attachment/1', array(), array(), 
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            )
        );

        $this->assertEquals($client->getResponse()->getStatusCode(), 403);
    }
}
