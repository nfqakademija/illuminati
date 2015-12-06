<?php

namespace Illuminati\ProductBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Tests\ClientTest;

/**
 * Class CartControllerTest
 * @package Illuminati\ProductBundle\Tests\Controller
 */
class CartControllerTest extends WebTestCase
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

    public function testAddAction()
    {
        $csrfToken = $this->client
            ->getContainer()
            ->get('form.csrf_provider')
            ->generateCsrfToken('IlluminatiCartBundleItemAddType');

        $this->client->followRedirects();

        $crawler = $this->client->request('POST', '/cart/add', [
            'IlluminatiCartBundleItemAddType' => [
                'orderId' => 1,
                'productId' => 1,
                '_token' => $csrfToken,
            ]
        ]);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Product list")')->count()
        );
    }

    public function testCheckoutAction()
    {
        // Add cart item to session
        $session = $this->client->getContainer()->get('session');
        $session->set('cart', [1=>1]);
        $session->save();

        $crawler = $this->client->request('GET', '/cart/checkout/1');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Checkout")')->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Demo pica 1")')->count()
        );
    }
}
