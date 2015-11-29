<?php

namespace Illuminati\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    /**
     * Sets Container
     *
     * @param ContainerInterface|null $container Container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Loads Fixtures
     *
     * @param ObjectManager $manager Object Manager
     */
    public function load(ObjectManager $manager)
    {
        $users = array(
            array('user1@mailinator.com','pass',array('ROLE_USER'),'Tomas','Tomukas','user1'),
            array('user2@mailinator.com','pass',array('ROLE_USER'),'Petras','Petraitis','user2'),
            array('user3@mailinator.com','pass',array('ROLE_USER'),'Jonas','Jonaitis','user3'),
            array('user4@mailinator.com','pass',array('ROLE_USER'),'Mindaugas','Minde','user4'),
            array('user5@mailinator.com','pass',array('ROLE_USER'),'Dmitrij','Medvedev','user5'),
            array('user6@mailinator.com','pass',array('ROLE_USER'),'Evaldas','Evaldass','user6'),
            array('user7@mailinator.com','pass',array('ROLE_USER'),'Marius','Mariuss','user7'),
            array('user8@mailinator.com','pass',array('ROLE_USER'),'Andrius','Andriuss','user8'),
            array('user9@mailinator.com','pass',array('ROLE_USER'),'Arturas','Arturass','user9'),
            array('user10@mailinator.com','pass',array('ROLE_USER'),'Tadas','Tadass','user10'),
        );
        $userManager = $this->container->get('fos_user.user_manager');
        $i = 0;
        foreach ($users as $userData) {
            $i++;
            $User = $userManager->createUser();

            $User->setUsername($userData[0]);
            $User->setUsernameCanonical($userData[0]);
            $User->setEmail($userData[0]);
            $User->setEmailCanonical($userData[0]);
            $User->setEnabled(true);
            $User->setPlainPassword($userData[1]);
            $User->setRoles($userData[2]);
            $User->setName($userData[3]);
            $User->setSurname($userData[4]);

            $manager->persist($User);

            $this->addReference("user{$i}", $User);
        }

        $manager->flush();
    }

    /**
     * Order of fixture execution
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}