<?php

namespace Illuminati\CartBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Illuminati\CartBundle\Controller\CartControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CartListener
 * @package Illuminati\CartBundle\EventListener
 */
class CartListener
{
    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * ProductListener constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FilterControllerEvent $event
     */
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

        if ($controller[0] instanceof CartControllerInterface) {

            $user = $controller[0]
                ->get('security.token_storage')
                ->getToken()
                ->getUser();

            // Checks if an order belongs to user
            $orderId = $event->getRequest()->get('orderId');
            if ($orderId) {
                $order = $this->entityManager
                    ->getRepository('IlluminatiOrderBundle:User_order')
                    ->findOneBy([
                        'hostOrderId' => $orderId,
                        'usersId' => $user->getId(),
                        'deleted' => 0,
                    ]);

                if (!$order) {
                    throw new NotFoundHttpException('Order not found!');
                }
            }
        }
    }
}