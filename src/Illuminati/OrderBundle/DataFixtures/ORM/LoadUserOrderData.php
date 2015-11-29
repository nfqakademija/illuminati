<?php

namespace Illuminati\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\OrderBundle\Entity\User_order;

class LoadUserOrderData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=5; $i++)
        {
            $userOrder = new User_order();
            $userOrder->setDeleted(0);
            $userOrder->setPayed(0);
            $userOrder->setHostOrderId($this->getReference('hostOrder1'));
            $userOrder->setUsersId($this->getReference("user{$i}"));
            $manager->persist($userOrder);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 7;
    }
}