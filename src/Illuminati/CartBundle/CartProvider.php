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
    public $em;

    /**
     * @var array
     */
    public $parameters;

    /**
     * CartProvider constructor.
     * @param Session $session
     * @param EntityManagerInterface $em
     * @param $parameters
     */
    public function __construct(Session $session, EntityManagerInterface $em, $parameters)
    {
        $this->parameters = $parameters;

        $this->session = $session;
        $this->cart = $this->session->get('cart');

        $this->em = $em;
    }

    /**
     * @param $productId
     * @return bool|Product
     */
    public function getItem($productId)
    {
        if (isset($this->cart[$productId])) {
            return $this->em
                ->getRepository('ProductBundle:Product')
                ->find($productId);
        }
        return false;
    }

    /**
     * @param $product_id
     * @return integer
     */
    public function getQuantity($product_id)
    {
        if (isset($this->cart[$product_id])) {
            return $this->cart[$product_id];
        }
        return 0;
    }

    /**
     * @param $product_id
     * @return null
     */
    public function addItem($product_id)
    {
        if ($this->cart) {
            if (isset($this->cart[$product_id])) {
                // append to existing item
                $this->cart[$product_id] += 1;
            } else {
                // add item first time
                $this->cart[$product_id] = 1;
            }
        } else {
            // add first time if cart doesn't exists
            $this->cart = [
                $product_id => 1
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
