<?php

namespace Illuminati\ProductBundle;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\Entity\Supplier;

/**
 * Interface ProductProviderInterface
 * @package Illuminati\ProductBundle
 */
interface ProductProviderInterface
{
    /**
     * @param Supplier $supplier
     * @param EntityManagerInterface $entityManager
     * @return mixed
     */
    public function import(Supplier $supplier, EntityManagerInterface $entityManager);

    /**
     * @return mixed
     */
    public function getCurrency();

    /**
     * @param $currency
     * @return mixed
     */
    public function setCurrency($currency);
}
