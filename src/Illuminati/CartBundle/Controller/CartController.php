<?php

namespace Illuminati\CartBundle\Controller;

use Illuminati\CartBundle\Form\CheckoutType;
use Illuminati\OrderBundle\Entity\User_order_details;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CartController
 * @package Illuminati\CartBundle\Controller
 */
class CartController extends Controller
{
    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirm($orderId)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $translator = $this->get('translator');

        $entityManager = $this->getDoctrine()->getManager();

        $session = $this->get('session');
        $cart = $this->get('cart.provider');

        $order = $this->getDoctrine()
            ->getRepository('IlluminatiOrderBundle:Host_order')
            ->find($orderId);

        $entityManager
            ->getRepository('IlluminatiOrderBundle:User_order_details')
            ->deleteAllUserOrderedDetails(
                $user->getId(),
                $order->getId()
            );

        foreach ($cart->getStorage() as $productId => $quantity) {

            $product = $cart->getItem($productId);

            $userOrderDetails = new User_order_details();

            $userOrderDetails->setHostOrderId($order);
            $userOrderDetails->setProductId($product);
            $userOrderDetails->setQuantity($quantity);
            $userOrderDetails->setUserId($user);

            $entityManager->persist($userOrderDetails);
        }

        $entityManager->flush();

        $session->remove('cart');

        $session->getFlashBag()->add('success', $translator->trans('cart.confirm_success'));

        return $this->redirectToRoute(
            'host_order_summary',
            ['id' => $orderId]
        );
    }

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
            return $this->confirm($orderId);
        }

        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'orderId' => $orderId,
            'checkoutForm' => $form->createView(),
        ]);
    }

    /**
     * @param $orderId
     * @return Response
     */
    public function checkoutItems($orderId)
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

        return $this->render('CartBundle:Cart:checkout.items.html.twig', [
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
        $request = $this->get('request');

        $cart = $this->get('cart.provider');

        if ($quantity = $request->request->get('quantity')) {
            $cart->updateQuantity($productId, $quantity);
        } else {
            $cart->addItem($productId);
        }

        if ($request->isMethod('POST')) {
            return $this->checkoutItems($orderId);
        }

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

        if ($this->get('request')->isMethod('POST')) {
            return $this->checkoutItems($orderId);
        }

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
        $cart = $this->get('cart.provider');

        return $this->render('CartBundle:Cart:widget.html.twig', [
                'cart' => $cart,
                'orderId' => $orderId,
        ]);
    }
}
