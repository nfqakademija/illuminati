<?php
// src/Illuminati/ProductBundle/Menu/Builder.php
namespace Illuminati\ProductBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class Builder
 * @package Illuminati\ProductBundle\Menu
 */
class Builder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function suppliersMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $SupplierEntities = $em->getRepository('ProductBundle:Supplier')->findAll();

        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav nav-pills nav-stacked',
            ),
        ));

        foreach ($SupplierEntities as $supplier) {
            $menu->addChild($supplier->getName(), array(
                'route' => 'supplier_show',
                'routeParameters' => array(
                    'id'=>$supplier->getId()
                ),
            ));
        }

        return $menu;
    }
}