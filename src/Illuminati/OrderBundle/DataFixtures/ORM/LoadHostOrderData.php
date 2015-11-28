<?php

namespace Illuminati\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\OrderBundle\Entity\Host_order;

class LoadHostOrderData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=5; $i++)
        {
            $date = new \DateTime("12/1{$i}/2015");
            $hostOrder = new Host_order();
            $hostOrder->setUsersId($this->getReference("user{$i}"));
            $hostOrder->setTitle("Awesome order{$i}");
            $hostOrder->setCloseDate($date);
            $hostOrder->setDescription('Awesome order for awesome people');
            $hostOrder->setSupplierId($this->getReference('supplier'));
            $this->addReference("hostOrder{$i}", $hostOrder);

            $manager->persist($hostOrder);
            $manager->flush();
        }

    }

    public function getOrder()
    {
        return 4;
    }
}