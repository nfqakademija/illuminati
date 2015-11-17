<?php

namespace Illuminati\OrderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $this->logIn();

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/order');

        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("New order creation")')->count() > 0);
        $this->assertTrue($crawler->filter('form')->count() > 0);
    }

    public function testCreateAction()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/order/new');

        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("New order creation")')->count() > 0);
        $this->assertTrue($crawler->filter('form')->count() > 0);
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken(
            'admin', null, $firewall, array('ROLE_ADMIN')
        );

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


}
