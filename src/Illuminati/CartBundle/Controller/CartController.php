<?php

namespace Illuminati\CartBundle\Controller;

use Illuminati\CartBundle\Form\CartItemAddType;
use Illuminati\CartBundle\Form\CartItemDeleteType;
use Illuminati\CartBundle\Form\CartItemUpdateType;
use Illuminati\OrderBundle\Entity\User_order_details;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CartController
 * @package Illuminati\CartBundle\Controller
 */
class CartController extends Controller implements CartControllerInterface
{
    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmAction($orderId)
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

        $session->getFlashBag()->add(
            'success',
            $translator->trans('cart.confirm_success')
        );

        return $this->redirectToRoute(
            'host_order_summary',
            ['id' => $orderId]
        );
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\Form\Form
     */
    private function createCheckoutConfirmForm($orderId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cart_checkout_confirm', ['orderId' => $orderId]))
            ->setMethod('PUT')
            ->add('save', 'submit', [
                'label' => 'cart.confirm',
                'attr' => ['class'=>'btn btn-primary']
            ])->getForm();
    }

    /**
     * @param $orderId
     * @return Response
     */
    public function checkoutAction($orderId)
    {
        $cart = $this->get('cart.provider');
        $products = $cart->getItems();

        if (empty($products)) {
            return $this->redirectToRoute('product', ['orderId' => $orderId]);
        }

        $cartItemUpdateForms = $this->createCartItemUpdateTypeForms($products, $orderId);
        $cartItemDeleteForms = $this->createCartItemDeleteTypeForms($products, $orderId);

        return $this->render('CartBundle:Cart:checkout.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'orderId' => $orderId,
            'cartItemUpdateForms' => $cartItemUpdateForms,
            'cartItemDeleteForms' => $cartItemDeleteForms,
            'checkoutConfirmForm' => $this->createCheckoutConfirmForm($orderId)->createView()
        ]);
    }

    /**
     * @param $products
     * @param $orderId
     * @return array
     */
    private function createCartItemUpdateTypeForms($products, $orderId)
    {
        $cartItemUpdateForms = [];
        $cart = $this->get('cart.provider');

        $redirectUrl = $this->generateUrl('cart_checkout', ['orderId' => $orderId]);

        foreach ($products as $product) {

            $cartItemUpdateForms[$product->getId()] = $this->createForm(
                new CartItemUpdateType(),
                [
                    'orderId' => $orderId,
                    'quantity' => $cart->getQuantity($product->getId()),
                    'productId' => $product->getId(),
                    'redirectUrl' => $redirectUrl
                ],
                [
                    'action' => $this->generateUrl('cart_update')
                ]
            )->createView();
        }

        return $cartItemUpdateForms;
    }

    /**
     * @param $products
     * @param $orderId
     * @return array
     */
    private function createCartItemDeleteTypeForms($products, $orderId)
    {
        $cartItemDeleteForms = [];

        foreach ($products as $product) {

            $cartItemDeleteForms[$product->getId()] = $this->createForm(
                new CartItemDeleteType(),
                [
                    'orderId' => $orderId,
                    'productId' => $product->getId(),
                    'redirectUrl' => $this->generateUrl('cart_checkout', ['orderId' => $orderId])
                ],
                [
                    'action' => $this->generateUrl('cart_remove')
                ]
            )->createView();
        }

        return $cartItemDeleteForms;
    }

    /**
     * @param $orderId
     * @return Response
     */
    public function checkoutItems($orderId, $products)
    {
        $cart = $this->get('cart.provider');

        $cartItemUpdateForms = $this->createCartItemUpdateTypeForms($products, $orderId);
        $cartItemDeleteForms = $this->createCartItemDeleteTypeForms($products, $orderId);

        return $this->render('CartBundle:Cart:checkout.items.html.twig', [
            'cart' => $cart,
            'products' => $products,
            'orderId' => $orderId,
            'cartItemUpdateForms' => $cartItemUpdateForms,
            'cartItemDeleteForms' => $cartItemDeleteForms,
            'checkoutConfirmForm' => $this->createCheckoutConfirmForm($orderId)->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
        $session = $this->get('session');

        $cart = $this->get('cart.provider');

        $form = $this->createForm(new CartItemAddType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $orderId = $form->get('orderId')->getViewData();
            if (!$this->checkIfOrderBelongsToUser($orderId)) {
                throw new NotFoundHttpException();
            }

            $productId = $form->get('productId')->getViewData();

            $itemAdded = $cart->addItem($productId);
            if ($itemAdded) {
                $session->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('cart.item_added')
                );
            }

        } else {
            throw new NotFoundHttpException();
        }

        $redirectUrl = $form->get('redirectUrl')->getViewData();
        if (!empty($redirectUrl)) {
            return $this->redirect($redirectUrl);
        } else {
            return $this->redirectToRoute('product', [
                'orderId' => $form->get('orderId')->getViewData()
            ]);
        }
    }

    /**
     * @param $orderId
     * @return \Illuminati\OrderBundle\Entity\User_order|null|object
     */
    private function checkIfOrderBelongsToUser($orderId)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        return $entityManager
            ->getRepository('IlluminatiOrderBundle:User_order')
            ->findOneBy([
                'hostOrderId' => $orderId,
                'usersId' => $user->getId(),
                'deleted' => 0,
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Request $request)
    {
        $cart = $this->get('cart.provider');

        $form = $this->createForm(new CartItemUpdateType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $orderId = $form->get('orderId')->getViewData();
            if (!$this->checkIfOrderBelongsToUser($orderId)) {
                throw new NotFoundHttpException();
            }

            $productId = $form->get('productId')->getViewData();

            $quantity = (int) $form->get('quantity')->getViewData();
            $quantity = abs($quantity);
            if ($quantity) {
                $cart->updateQuantity($productId, $quantity);
            }

        } else {
            throw new NotFoundHttpException();
        }

        return $this->checkoutItems($orderId, $cart->getItems());
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Request $request)
    {
        $cart = $this->get('cart.provider');

        $form = $this->createForm(new CartItemDeleteType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $cart->removeItem(
                $form->get('productId')->getViewData()
            );
        }

        $redirectUrl = $form->get('redirectUrl')->getViewData();
        if (!empty($redirectUrl)) {
            return $this->redirect($redirectUrl);
        } else {
            return $this->redirectToRoute('product', [
                'orderId' => $form->get('orderId')->getViewData()
            ]);
        }
    }

    /**
     * @param $orderId
     * @param $redirectUrl
     * @return Response
     */
    public function sidebarWidgetAction($orderId, $redirectUrl = false)
    {
        $cart = $this->get('cart.provider');

        $deleteForms = array();

        $items = $cart->getItems();
        if ($items) {
            foreach ($items as $item) {
                $deleteForms[$item->getId()] = $this->createForm(
                    new CartItemDeleteType(),
                    [
                        'orderId' => $orderId,
                        'productId' => $item->getId(),
                        'redirectUrl' => $redirectUrl
                    ],
                    ['action' => $this->generateUrl('cart_remove')]
                )->createView();
            }
        }

        return $this->render('CartBundle:Cart:widget.html.twig', [
            'cart' => $cart,
            'orderId' => $orderId,
            'deleteForms' => $deleteForms,
        ]);
    }
}
