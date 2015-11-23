<?php

namespace Illuminati\OrderBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class HostOrderParticipationChecker
{

    protected $em;

    protected $securityToken;
    /**
     * Injecting dependencies
     *
     * @param EntityManagerInterface $em Entity manager
     * @param TokenStorageInterface  $st Security token thing
     */
    public function __construct(
        EntityManagerInterface $em, TokenStorageInterface $st
    ) {
        $this->em = $em;
        $this->securityToken = $st;
    }

    /**
     * Checks if the authorized user is a participant of the hosted order.
     *
     * @param int $hostOrderId Host order id
     *
     * @throws \InvalidArgumentException When wrong parameter is passed to function
     *
     * @return bool|\Illuminati\OrderBundle\Entity\Host_order Host order Object
     */
    public function check($hostOrderId)
    {
        if (!is_int($hostOrderId)) {
            throw new \InvalidArgumentException(
                "Function only accepts integers. Input was : {$hostOrderId} ("
                .gettype($hostOrderId).")"
            );
        }

        $hostOrderObj = $this->em->getRepository("IlluminatiOrderBundle:Host_order")
            ->find($hostOrderId);

        if (empty($hostOrderObj)) {
            // No host order found with provided ID
            return false;
        }

        $userObj = $this->securityToken->getToken()->getUser();

        // Getting list of user orders assigned to the main Order

        $userOrders = $hostOrderObj->getUserOrders()->getValues();

        if (empty($userOrders)) {
            // No users orders in the Host order
            return false;
        }

        foreach ($userOrders as $usrOrd) {
            if ($usrOrd->getUsersId() === $userObj) {
                return $hostOrderObj;
            }
        }

        return false;
    }
}