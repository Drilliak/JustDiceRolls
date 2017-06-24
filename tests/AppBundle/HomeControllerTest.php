<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testindex(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertContains('Nouvelle partie', $client->getResponse()->getContent());

    }
}
