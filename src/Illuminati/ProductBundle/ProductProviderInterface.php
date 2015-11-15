<?php

namespace Illuminati\ProductBundle;

use Doctrine\ORM\EntityManagerInterface;
use Illuminati\ProductBundle\Entity\Supplier;

interface ProductProviderInterface
{
    public function import(Supplier $supplier, EntityManagerInterface $em);
}