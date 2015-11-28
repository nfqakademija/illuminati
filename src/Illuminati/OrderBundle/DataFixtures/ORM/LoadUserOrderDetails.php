<?php

namespace Illuminati\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\OrderBundle\Entity\User_order_details;

class LoadUserOrderDetails extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=5; $i++)
        {
            $userOrderDetails = new User_order_details();
            $userOrderDetails->setHostOrderId($this->getReference('hostOrder1'));
            $userOrderDetails->setProductId($this->getReference('product1'));
            $userOrderDetails->setQuantity(5);
            $userOrderDetails->setUserId($this->getReference("user{$i}"));
            $manager->persist($userOrderDetails);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 8;
    }
}