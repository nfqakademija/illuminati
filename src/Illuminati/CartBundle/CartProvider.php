<?php

namespace Illuminati\CartBundle;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * CartProvider constructor.
     * @param Session $session
     */
    function __construct(Session $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->cart = $this->session->get('cart');

        $this->em = $em;
    }

    /**
     * @param $product_id
     * @return bool|object
     */
    public function getItem($product_id)
    {
        if(isset($this->cart[$product_id])) {
            return $this->em
                ->getRepository('ProductBundle:Product')
                ->find($product_id);
        }
        return false;
    }

    /**
     * @param $product_id
     * @return integer
     */
    public function getQuantity($product_id)
    {
        if(isset($this->cart[$product_id])) {
            return $this->cart[$product_id];
        }
        return 0;
    }

    public function addItem($product_id)
    {
        if($this->cart) {
            if(isset($this->cart[$product_id])) {
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
        if(isset($this->cart[$product_id])) {
            unset($this->cart[$product_id]);
            $this->session->set('cart', $this->cart);
            return true;
        }
        return false;
    }

    public function getItems()
    {
        // Items added to cart
        $items = [];

        if($this->cart) {
            foreach ($this->cart as $product_id => $quantity) {
                $items[$product_id] = $this->getItem($product_id);
            }
        }

        return $items;
    }

    public function getAmount()
    {
        $amount = 0;

        if($items = $this->getItems()) {
            foreach($items as $item) {
                $amount += $item->getPrice() * $this->cart[$item->getId()];
            }
        }
        return $amount;
    }

    public function geItemTotalAmount($id, $price)
    {
        $quantity = $this->getQuantity($id);
        return ($quantity * $price);
    }


}