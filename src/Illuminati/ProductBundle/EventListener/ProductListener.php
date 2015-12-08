<?php

namespace Illuminati\ProductBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Illuminati\ProductBundle\Controller\ProductControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * ProductController event listener checks if an order belongs to user
 * and loads cart data from database
 * Class ProductListener
 * @package Illuminati\ProductBundle\EventListener
 */
class ProductListener
{
    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * @var Session
     */
    public $session;

    /**
     * @var TranslatorInterface
     */
    public $translator;

    /**
     * ProductListener constructor.
     * @param EntityManager $entityManager
     * @param Session $session
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $entityManager, Session $session, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /**
         * @var $controller Controller
         */
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

                $redirectUrl = $controller[0]->generateUrl('host_order_summary', [
                    'id' => $orderId
                ]);

                $closeDate = $order->getHostOrderId()->getCloseDate();
                $now = new \DateTime('now');
                if ($closeDate < $now) {

                    $this->session->getFlashBag()->add(
                        'info',
                        $this->translator->trans('product.order_expired')
                    );

                    // redirect to summary if order is expired
                    $event->setController(function() use ($redirectUrl) {
                        return new RedirectResponse($redirectUrl);
                    });
                }

                // redirect to summary if order is closed or confirmed
                if ($order->getHostOrderId()->getStateId() == 0) {

                    $this->session->getFlashBag()->add(
                        'info',
                        $this->translator->trans('product.order_closed')
                    );

                    $event->setController(function() use ($redirectUrl) {
                        return new RedirectResponse($redirectUrl);
                    });
                }
            }

            // Load cart items from database
            $cart = $controller[0]->get('cart.provider');
            if ($cart->getStorage() === null) {
                $cart->load($user->getId(), $orderId);
            }
        }
    }
}