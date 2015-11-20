<?php

namespace Illuminati\ProductBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\ProductBundle\Entity\Product;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=0; $i<10; $i++) {

            $product = new Product();

            $product->setTitle('Demo pica ' . ($i+1));
            $product->setImage('http://www.cili.lt/wp-content/uploads/2015/09/STUDENTU.jpg');
            $product->setDeleted(0);
            $product->setCurrency('EUR');
            $product->setDescription('Virtas kiaulienos kumpis, dešrelės, mocarela, sūris, pomidorų padažas, aliejaus ir česnako padažas, raudonėliai.');
            $product->setSupplier($this->getReference('supplier'));

            $this->addReference("product{$i}",$product);

            $manager->persist($product);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 2;
    }
}