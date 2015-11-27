<?php

namespace Illuminati\CartBundle\Controller;

use Illuminati\CartBundle\Form\CheckoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CartController
 * @package Illuminati\CartBundle\Controller
 */
class CartController extends Controller
{
    /**
     * @param $orderId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkoutAction($orderId, Request $request)
    {
        $cart = $this->get('cart.provider');
        $products = $cart->getItems();

        $data = [];
        foreach ($products as $product) {
            $data['items'][] = [
                'quantity' => $cart->getQuantity($product->getId()),
                'product_id' => $product->getId(),
            ];
        }

        $form = $this->createForm(new CheckoutType(), $data);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->get('items')->getData();
            foreach ($data as $cartItem) {
                $cart->updateQuantity(
                    $cartItem['product_id'],
                    $cartItem['quantity']
                );
            }
        }

        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'orderId' => $orderId,
            'checkoutForm' => $form->createView(),
        ]);
    }

    /**
     * @param $productId
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction($productId, $orderId)
    {
        $cart = $this->get('cart.provider');
        $cart->addItem($productId);

        return $this->redirectToRoute('product', [
            'orderId' => $orderId
        ]);
    }

    /**
     * @param $productId
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($productId, $orderId)
    {
        $cart = $this->get('cart.provider');
        $cart->removeItem($productId);
        return $this->redirectToRoute('product', [
            'orderId' => $orderId
        ]);
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sidebarWidgetAction($orderId)
    {
        return $this->render('CartBundle:Cart:widget.html.twig', [
                'cart' => $this->get('cart.provider'),
                'orderId' => $orderId,
        ]);
    }
}
