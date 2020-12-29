<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testSomething()
    {
        dump('hello 1'); // This one is displayed
        $client = static::createClient();
        dump('hello 2'); // This one is NOT displayed
        $crawler = $client->request('GET', '/');
        dump('hello 3'); // This one is NOT displayed

        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
