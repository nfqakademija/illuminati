<?php

namespace Illuminati\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Illuminati\ProductBundle\Entity\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Product controller.
 *
 */
class ProductController extends Controller
{
    /**
     * @param $orderId
     * @return object Host_order
     */
    public function getRelatedOrder($orderId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository('IlluminatiOrderBundle:Host_order')->find($orderId);
        if (!$order) {
            throw new NotFoundHttpException("An order doesn't exists");
        }
        return $order;
    }

    /**
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($orderId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $order = $this->getRelatedOrder($orderId);

        $cart = $this->get('cart.provider');
        if ($cart->getStorage() === null) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $cart->load($user->getId(), $order->getId());
        }

        $productEntities = $entityManager->getRepository('ProductBundle:Product')->findBy([
            'supplier' => $order->getSupplierId()
        ]);

        return $this->render('ProductBundle:Product:index.html.twig', [
            'ProductEntities' => $productEntities,
            'orderId' => $order->getId(),
        ]);
    }

    /**
     * Finds and displays a Product entity.
     * @param $id
     * @param $orderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id, $orderId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('ProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        return $this->render('ProductBundle:Product:show.html.twig', [
            'entity' => $entity,
            'orderId' => $orderId,
        ]);
    }
}
