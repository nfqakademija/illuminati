<?php


namespace Illuminati\OrderBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HostOrderJoinChecker
{
    protected $em;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts)
    {
        $this->em = $em;
        $this->tokenStorage = $ts;
    }

    /**
     * Checks if the currently logged in user can join the host order
     *
     * @param string $hostOrderToken Host order invite token
     *
     * @return bool|\Illuminati\OrderBundle\Entity\Host_order|int
     */
    public function check($hostOrderToken)
    {
        $hostOrder = $this->em
            ->getRepository('IlluminatiOrderBundle:Host_order')
            ->findOneBy(
                [
                    'stateId'    => 1,
                    'orderToken' => $hostOrderToken,
                    'deleted'    => 0
                ]
            );

        if (empty($hostOrder)) {
            // no host order found with provided order token
            return false;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        // checking if the user already participates in the order

        $userOrder = $this->em
            ->getRepository('IlluminatiOrderBundle:User_order')
            ->findOneBy(
                [
                    'hostOrderId' => $hostOrder,
                    'usersId'     => $user
                ]
            );

        if (!empty($userOrder)) {
            // User already participates in the host order
            return $hostOrder->getId();
        }

        return $hostOrder;
    }
}