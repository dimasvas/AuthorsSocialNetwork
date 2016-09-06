<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * Description of AuthorFunctionalTest
 *
 * @author dimas
 */
class AuthorControllerTest extends WebTestCase
{
    public function testListAction () 
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/ru/author/list');
        
        $this->assertEquals(
           4,
            $crawler->filter('.author-list')->count()
        );
    }
    
    public function testShowAction ()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/ru/author/1');
        
        $heading = $crawler->filter('h1')->eq(0)->text();
        
        //Check if Author name is right
        $this->assertContains('La', $heading );
        
        //Check if table empty and there is corresponding notification
        $this->assertEquals(
            1, 
            $crawler->filter('#no-compositions')->count());
    }
}
