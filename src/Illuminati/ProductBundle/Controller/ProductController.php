<?php

namespace Illuminati\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ProductController
 * @package Illuminati\ProductBundle\Controller
 */
class ProductController extends Controller implements ProductControllerInterface
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

        $productEntities = $entityManager->getRepository('ProductBundle:Product')->findBy([
            'supplier' => $order->getSupplierId()
        ]);

        return $this->render('ProductBundle:Product:index.html.twig', [
            'ProductEntities' => $productEntities,
            'order' => $order,
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
            'order' => $this->getRelatedOrder($orderId),
        ]);
    }
}
