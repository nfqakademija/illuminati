<?php

namespace Illuminati\OrderBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Illuminati\OrderBundle\Entity\Order_participants;

class LoadOrderParticipantsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=5; $i++) {
            $orderParticipants = new Order_participants();
            $orderParticipants->setHostOrderId($this->getReference('hostOrder1'));
            $orderParticipants->setUsersEmail("user{$i}@mailinator.com");
            $orderParticipants->setDeleted(0);
            $orderParticipants->setConfirmed(1);
            $orderParticipants->setInviteToken("inviteToken{$i}");

            $manager->persist($orderParticipants);
            $manager->flush();
            }
    }

    public function getOrder()
    {
        return 6;
    }
}