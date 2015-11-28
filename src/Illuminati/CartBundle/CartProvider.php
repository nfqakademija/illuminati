<?php

namespace Illuminati\CartBundle;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CartProvider
 * @package Illuminati\CartBundle
 */
class CartProvider implements CartProviderInterface
{
    /**
     * @var Session
     */
    public $session;

    /**
     * @var array
     */
    public $cart;

    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    /**
     * @var array
     */
    public $parameters;

    /**
     * CartProvider constructor.
     * @param Session $session
     * @param EntityManagerInterface $entityManager
     * @param $parameters
     */
    public function __construct(Session $session, EntityManagerInterface $entityManager, $parameters)
    {
        $this->parameters = $parameters;

        $this->session = $session;
        $this->cart = $this->session->get('cart');

        $this->entityManager = $entityManager;
    }

    /**
     * @return array|mixed
     */
    public function getStorage()
    {
        return $this->cart;
    }

    /**
     * @param int $userId
     * @param int $orderId
     * @return null
     */
    public function load($userId, $orderId)
    {
        $userOrderDetails = $this->entityManager
            ->getRepository('IlluminatiOrderBundle:User_order_details')
            ->getAllUserOrderedDetails($userId, $orderId);

        if ($userOrderDetails) {
            foreach ($userOrderDetails as $orderDetailsItem) {
                $this->addItem(
                    $orderDetailsItem->getProductId()->getId(),
                    $orderDetailsItem->getQuantity()
                );
            }
        }
    }

    /**
     * @param $productId
     * @return bool|Product
     */
    public function getItem($productId)
    {
        if (isset($this->cart[$productId])) {
            return $this->entityManager
                ->getRepository('ProductBundle:Product')
                ->find($productId);
        }
        return false;
    }

    /**
     * @param $productId
     * @return integer
     */
    public function getQuantity($productId)
    {
        if (isset($this->cart[$productId])) {
            return $this->cart[$productId];
        }
        return 0;
    }

    /**
     * @param $productId
     * @return null
     */
    public function addItem($productId)
    {
        if ($this->cart) {
            if (isset($this->cart[$productId])) {
                // append to existing item
                $this->cart[$productId] += 1;
            } else {
                // add item first time
                $this->cart[$productId] = 1;
            }
        } else {
            // add first time if cart doesn't exists
            $this->cart = [
                $productId => 1
            ];
        }

        $this->session->set('cart', $this->cart);
    }

    /**
     * @param $product_id
     * @return bool
     */
    public function removeItem($product_id)
    {
        if (isset($this->cart[$product_id])) {
            unset($this->cart[$product_id]);
            $this->session->set('cart', $this->cart);
            return true;
        }

        return false;
    }

    /**
     * @return Product[]
     */
    public function getItems()
    {
        // Items added to cart
        $items = [];

        if ($this->cart) {
            foreach ($this->cart as $product_id => $quantity) {
                $items[$product_id] = $this->getItem($product_id);
            }
        }

        return $items;
    }

    /**
     * @return int|string
     */
    public function getAmount()
    {
        $amount = 0;

        if ($items = $this->getItems()) {
            foreach ($items as $item) {
                $amount += $item->getPrice() * $this->getQuantity($item->getId());
            }
        }
        return $amount;
    }

    /**
     * @param $id
     * @param $price
     * @return float
     */
    public function geItemTotalAmount($id, $price)
    {
        $quantity = $this->getQuantity($id);
        return ($quantity * $price);
    }

    /**
     * @param $id
     * @param $quantity
     * @return null
     */
    public function updateQuantity($id, $quantity)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id] = $quantity;
        }
    }

    /**
     * @return bool|string
     */
    public function getProviderCurrency()
    {
        return isset($this->parameters['currency']) ? $this->parameters['currency'] : false;
    }
}
