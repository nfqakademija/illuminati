<?php

namespace Illuminati\ProductBundle;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\Entity\Supplier;

interface ProductProviderInterface
{
    /**
     * @param Supplier $supplier
     * @param EntityManagerInterface $em
     * @return mixed
     */
    public function import(Supplier $supplier, EntityManagerInterface $em);

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