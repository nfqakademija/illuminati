<?php

namespace Illuminati\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\OrderBundle\Entity\Host_order_state;

class LoadHostOrderStateData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
            $hostOrderState = new Host_order_state();
            $hostOrderState->setState('open');
            $manager->persist($hostOrderState);
            $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}