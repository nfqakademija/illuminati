<?php

// src/AppBundle/Tests/Controller/PostControllerTest.php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPost()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/contacts');

        $this->assertContains('5', $client->getResponse()->getContent());
    }
}