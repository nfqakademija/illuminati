<?php

namespace Illuminati\ProductBundle\EventListener;

use Illuminati\ProductBundle\Controller\ProductControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * ProductController event listener checks to load cart data from database
 * Class ProductListener
 * @package Illuminati\ProductBundle\EventListener
 */
class ProductListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof ProductControllerInterface) {

            $orderId = $event->getRequest()->get('orderId');

            $cart = $controller[0]->get('cart.provider');

            if ($cart->getStorage() === null) {
                $user = $controller[0]->get('security.token_storage')->getToken()->getUser();
                $cart->load($user->getId(), $orderId);
            }
        }
    }
}