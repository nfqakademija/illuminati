<?php

namespace Illuminati\CartBundle\Controller;

use Illuminati\CartBundle\Form\CheckoutType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    public function checkoutAction($order_id, Request $request)
    {
        $cart = $this->get('cart.provider');
        $products = $cart->getItems();

        $data = [];
        foreach($products as $product) {
            $data['items'][] = [
                'quantity' => $cart->getQuantity($product->getId()),
                'product_id' => $product->getId(),
            ];
        }

        $form = $this->createForm(new CheckoutType(), $data);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            // print_r($data); exit;
        }

        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'order_id' => $order_id,
            'checkout_form' => $form->createView(),
        ]);
    }

    public function addAction($product_id, $order_id)
    {
        $cart = $this->get('cart.provider');
        $cart->addItem($product_id);

        return $this->redirectToRoute('product', [
            'order_id' => $order_id
        ]);
    }

    public function removeAction($product_id, $order_id)
    {
        $cart = $this->get('cart.provider');
        $cart->removeItem($product_id);
        return $this->redirectToRoute('product', [
            'order_id' => $order_id
        ]);
    }

    public function sidebarWidgetAction($order_id)
    {
        return $this->render('CartBundle:Cart:widget.html.twig', [
                'cart' => $this->get('cart.provider'),
                'order_id' => $order_id,
            ]
        );
    }
}
