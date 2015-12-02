<?php

namespace Illuminati\ProductBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\User;
use Illuminati\OrderBundle\Entity\Host_order;
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

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Host_order
     */
    private $order;

    public function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user1@mailinator.com',
            'PHP_AUTH_PW'   => 'pass',
        ));

        $this->entityManager = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->getUser();
        $this->order = $this->getOrder($this->user);

        parent::setUp();
    }

    /**
     * @return User
     */
    private function getUser()
    {
        return $this->entityManager
            ->getRepository('IlluminatiUserBundle:User')
            ->findOneByUsername('user1@mailinator.com');
    }

    /**
     * @param User $user
     * @return Host_order
     */
    private function getOrder($user)
    {
        return $this->entityManager
            ->getRepository('IlluminatiOrderBundle:Host_order')
            ->findOneByUsersId($user);
    }

    public function testIndexAction()
    {
        $url = $this->client
            ->getContainer()
            ->get('router')
            ->generate('product', array(
                'orderId' => $this->order->getId(),
            ));

        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testShowAction()
    {
        $supplier = $this->order->getSupplierId();

        $product = $this->entityManager
            ->getRepository('ProductBundle:Product')
            ->findOneBySupplier($supplier);

        $url = $this->client
            ->getContainer()
            ->get('router')
            ->generate('product_show', array(
                'id' => $product->getId(),
                'orderId' => $this->order->getId(),
            ));

        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
