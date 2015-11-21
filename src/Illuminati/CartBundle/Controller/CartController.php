<?php

namespace Illuminati\CartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
{
    public function checkoutAction()
    {
        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'cart' => $this->get('cart.provider'),
        ]);
    }

    public function addAction($product_id)
    {
        $cart = $this->get('cart.provider');
        $cart->addItem($product_id);
        return $this->redirectToRoute('product');
    }

    public function removeAction($product_id)
    {
        $cart = $this->get('cart.provider');
        $cart->removeItem($product_id);
        return $this->redirectToRoute('product');
    }

    public function sidebarWidgetAction()
    {
        return $this->render('CartBundle:Cart:widget.html.twig', [
                'cart' => $this->get('cart.provider'),
            ]
        );
    }
}
