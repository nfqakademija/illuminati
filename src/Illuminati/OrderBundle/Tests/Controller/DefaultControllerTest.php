<?php

namespace Illuminati\OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
        $this->client = static::createAuthorizedClient();
    }

    public function testIndex()
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/order');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("New order creation")')->count() > 0);
        $this->assertTrue($crawler->filter('form')->count() > 0);
    }

    public function testCreateAction()
    {
        $crawler = $this->client->request('GET', '/order/new');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("New order creation")')->count() > 0);
        $this->assertTrue($crawler->filter('form')->count() > 0);
    }

    public function testEditOrderAction()
    {

        $crawler = $this->client->request('GET', '/order/1/edit');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Host order edit")')->count() > 0);
        $this->assertTrue($crawler->filter('form')->count() > 0);
        $this->assertTrue($crawler->filter('input[value="Awesome order1"]')->count() > 0);
        $this->assertTrue($crawler->filter('textarea:contains("Awesome order for awesome people")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Update")')->count() > 0);

    }


    public function testOrderSummaryAction()
    {
        $crawler = $this->client->request('GET', '/order/summary/1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Supplier")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Awesome order1")')->count() > 0);
    }

    public function testJoinedOrdersHistoryActon()
    {
        $crawler = $this->client->request('GET', '/history/joined');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('h1:contains("You haven\'t joined any orders")')->count() > 0);
    }

    public function testHostedOrdersHistoryAction()
    {
        $crawler = $this->client->request('GET', '/history/hosted');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('h1.page-header:contains("Orders you have hosted")')->count() > 0);
        $this->assertTrue($crawler->filter('h3.panel-title:contains("Awesome order1")')->count() > 0);
    }

    public function testConfirmOrderAction()
    {
        $crawler = $this->client->request('GET', '/order/confirm/1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertTrue($crawler->filter('h1:contains("Hosted Order Confirmation")')->count() > 0);
        $this->assertTrue($crawler->filter('table')->count() > 0);
        $this->assertTrue($crawler->filter('a:contains("Demo pica 2")')->count() > 0);
        $this->assertTrue($crawler->filter('a[href="/products/2/order/1"]')->count() > 0);
        $this->assertTrue($crawler->filter('td:contains("150 EUR")')->count() > 0);
        $this->assertTrue($crawler->filter('th:contains("150 EUR")')->count() > 0);
    }

    public function testPdfGenAction()
    {
        $this->client->request('GET', '/order/genpdf/1');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->headers->contains(
            'Content-Type',
            'application/pdf'
        ));
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = $container->get('session');
        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(array('email' => 'user1@mailinator.com'));
        $loginManager->loginUser($firewallName, $user);

        // save the login token into the session and put it in a cookie
        $container->get('session')->set(
            '_security_' . $firewallName,
            serialize($container->get('security.token_storage')->getToken())
        );
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
