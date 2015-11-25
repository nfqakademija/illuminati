<?php

namespace Illuminati\OrderBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HostOrderHostChecker
{
    protected $em;
    protected $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts)
    {
        $this->em = $em;
        $this->tokenStorage = $ts;
    }

    /**
     * Checks if the logged in user is the host of the hosted order
     *
     * @param integer $hostOrderId Host order id
     *
     * @return bool|\Illuminati\OrderBundle\Entity\Host_order
     */
    public function check($hostOrderId)
    {
        if (!is_int($hostOrderId)) {
            throw new \InvalidArgumentException(
                "Function only accepts integers. Input was : {$hostOrderId} ("
                .gettype($hostOrderId).")"
            );
        }

        $hostOrder = $this
            ->em
            ->getRepository('IlluminatiOrderBundle:Host_order')
            ->find($hostOrderId);

        // No order found with provided ID
        if (empty($hostOrder)) {
            return false;
        }

        // checking if the logged in user is the host of the order
        if ($hostOrder->getUsersId() != $this->tokenStorage->getToken()->getUser()) {
            return false;
        }

        return $hostOrder;
    }
}
