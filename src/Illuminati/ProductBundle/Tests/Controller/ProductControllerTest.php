<?php

namespace Illuminati\ProductBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Tests\ClientTest;

/**
 * Class ProductControllerTest
 * @package Illuminati\ProductBundle\Tests\Controller
 */
class ProductControllerTest extends WebTestCase
{
    /**
     * @var ClientTest
     */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user1@mailinator.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        parent::setUp();
    }

    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/products/1');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Product list")')->count()
        );
    }

    public function testShowAction()
    {
        $crawler = $this->client->request('GET', '/products/1/order/1');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Demo pica 1")')->count()
        );
    }
}
