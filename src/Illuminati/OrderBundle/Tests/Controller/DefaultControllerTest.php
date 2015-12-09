<?php

namespace Illuminati\OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
//        $this->client = $this->createAuthorizedClient();
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user1@mailinator.com',
            'PHP_AUTH_PW'   => 'pass',
        ));
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
        ob_start();
        $this->client->request('GET', '/order/genpdf/1');
        ob_end_clean();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->headers->contains(
            'Content-Type',
            'application/pdf'
        ));
    }

    public function testHostOrderCreation()
    {
        $this->client->followRedirects();

        $csrfToken = $this
            ->client
            ->getContainer()
            ->get('form.csrf_provider')
            ->generateCsrfToken('illuminati_orderbundle_host_order');


        $crawler = $this->client->request('GET', '/order/new');
        $buttonCrawlerNode = $crawler->selectButton('illuminati_orderbundle_host_order[submit]');

        $orderDate = date("Y-m-d H:i", time() + 86400);

        $form = $buttonCrawlerNode->form(
            [
                'illuminati_orderbundle_host_order[title]'       => 'testOrder',
                'illuminati_orderbundle_host_order[description]' => 'test Description ...',
                'illuminati_orderbundle_host_order[supplier_id]' => 1,
                'illuminati_orderbundle_host_order[closeDate]'   => $orderDate,
                'illuminati_orderbundle_host_order[_token]'      => $csrfToken
            ]
        );


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Order Summary")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("testOrder")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("test Description ...")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Due Date : ' . $orderDate . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Hosted By:")')->count() > 0);

    }

    public function testHostOrderEdit()
    {
        $this->client->followRedirects();

        $csrfToken = $this
            ->client
            ->getContainer()
            ->get('form.csrf_provider')
            ->generateCsrfToken('illuminati_orderbundle_host_order');


        $crawler = $this->client->request('GET', '/order/6/edit');
        $buttonCrawlerNode = $crawler->selectButton('illuminati_orderbundle_host_order[submit]');

        $orderDate = date("Y-m-d H:i", time() + 86400);

        $form = $buttonCrawlerNode->form(
            [
                'illuminati_orderbundle_host_order[title]'       => 'testOrder1',
                'illuminati_orderbundle_host_order[description]' => 'test Description1 ...',
                'illuminati_orderbundle_host_order[supplier_id]' => 1,
                'illuminati_orderbundle_host_order[closeDate]'   => $orderDate,
                'illuminati_orderbundle_host_order[_token]'      => $csrfToken
            ]
        );


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Order Summary")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("testOrder1")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("test Description1 ...")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Due Date : ' . $orderDate . '")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("Hosted By:")')->count() > 0);

    }

    public function testLeaveOrder()
    {
        $this->client->followRedirects();

        $csrfToken = $this
            ->client
            ->getContainer()
            ->get('form.csrf_provider')
            ->generateCsrfToken('form');

        $db = $this->client->getContainer()->get('database_connection');

        $hostOrderDeleted = $db->fetchAll(
            'SELECT deleted from host_order WHERE id = 6'
        );

        $userOrderDeleted = $db->fetchAll(
            'SELECT deleted from user_order WHERE id = 6'
        );

        $this->assertNotEmpty($hostOrderDeleted);
        $this->assertTrue($hostOrderDeleted[0]['deleted'] == 0);
        $this->assertNotEmpty($userOrderDeleted);
        $this->assertTrue($userOrderDeleted[0]['deleted'] == 0);

        $this->client->request(
            'POST',
            '/order/leave/6',
            [
                'form' => [
                    '_token' => $csrfToken
                ]
            ]
        );


        $hostOrderDeleted = $db->fetchAll(
            'SELECT deleted from host_order WHERE id = 6'
        );

        $userOrderDeleted = $db->fetchAll(
            'SELECT deleted from user_order WHERE id = 6'
        );

        $this->assertNotEmpty($hostOrderDeleted);
        $this->assertTrue($hostOrderDeleted[0]['deleted'] == 1);
        $this->assertNotEmpty($userOrderDeleted);
        $this->assertTrue($userOrderDeleted[0]['deleted'] == 1);

        $db->executeQuery(
            'UPDATE host_order SET deleted = 0 WHERE id = 6'
        );
    }

    public function testOrderConfirmation()
    {
        $csrfToken = $this
            ->client
            ->getContainer()
            ->get('form.csrf_provider')
            ->generateCsrfToken('form');

        $this->client->request(
            'POST',
            '/order/confirmed/6',
            [
                'form' => [
                    '_token' => $csrfToken
                ]
            ]
        );

        $orderCheck = $this
            ->client
            ->getContainer()
            ->get('database_connection')
            ->fetchAll(
                'SELECT state_id FROM host_order WHERE id = 6'
            );

        $this->assertNotEmpty($orderCheck);
        $this->assertTrue($orderCheck[0]['state_id'] == 0);
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
