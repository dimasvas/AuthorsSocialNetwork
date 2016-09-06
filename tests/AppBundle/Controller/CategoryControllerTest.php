<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of CategoryControllerTest
 *
 * @author dimas
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testCategoryAction($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    
    public function urlProvider()
    {
        return array(
            array('/ru/category/'),
            array('/ru/category/sub/1'),
            array('/ru/category/genre/1')
        );
    }
}
