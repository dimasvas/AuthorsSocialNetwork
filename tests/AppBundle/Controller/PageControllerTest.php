<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function urlProvider()
    {
        return array(
            array('/ru/page/contacts'),
            array('/ru/page/help'),
            array('/ru/page/about-us'),
            array('/ru/page/donate'),
            array('/ua/page/contacts'),
            array('/ua/page/help'),
            array('/ua/page/about-us'),
            array('/ua/page/donate')
        );
    }
}
