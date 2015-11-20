<?php

namespace Illuminati\CartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{
    public function checkoutAction(Request $request)
    {
        $session = $request->getSession();
        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'items' => $this->getCartProducts($request),
            'cart' => $session->get('cart'),
        ]);
    }

    public function addAction($product_id, Request $request)
    {
        $session = $request->getSession();

        if($cart = $session->get('cart')) {
            if(isset($cart[$product_id])) {
                $cart[$product_id] += 1;
            } else {
                $cart[$product_id] = 1;
            }
        } else {
            $cart = [
                $product_id => 1
            ];
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('product');
    }

    public function removeAction($product_id, Request $request)
    {
        $session = $request->getSession();
        $cart = $session->get('cart');

        if(isset($cart[$product_id])) {
            unset($cart[$product_id]);
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('product');
    }

    private function getCartProducts(Request $request)
    {
        $items = []; // items added to cart

        $session = $request->getSession();
        $cart = $session->get('cart');

        if($cart) {
            foreach ($cart as $product_id => $quantity) {
                $items[] = $this->getDoctrine()->getRepository('ProductBundle:Product')->find($product_id);
            }
        }

        return $items;
    }

    public function sidebarWidgetAction(Request $request)
    {
        $session = $request->getSession();
        return $this->render('CartBundle:Cart:widget.html.twig',
            [
                'items' => $this->getCartProducts($request),
                'cart' => $session->get('cart'),
            ]
        );
    }
}
