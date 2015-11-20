<?php

namespace Illuminati\ProductBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\ProductBundle\Entity\Supplier;

class LoadSupplierData extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $supplier = new Supplier();
        $supplier->setName('Čili pica');
        $supplier->setProvider('product.CiliPicaProductProvider');
        $supplier->setDeleted(null);

        $manager->persist($supplier);
        $manager->flush();

        $this->addReference('supplier', $supplier);
    }

    public function getOrder()
    {
        return 1;
    }
}