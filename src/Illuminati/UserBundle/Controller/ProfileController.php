<?php

namespace Illuminati\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

class ProfileController extends BaseController
{
    /**
     * Show the user
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $repo = $this
            ->getDoctrine()
            ->getRepository('IlluminatiOrderBundle:Host_order');

        $hostedOrdersCount = sizeof($repo->findHostedOrders($user->getId()));
        $joinedOrdersCount = sizeof($repo->findJoinedOrders($user->getId()));

        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'hostedOrdersCount' => $hostedOrdersCount,
            'joinedOrdersCount' => $joinedOrdersCount
        ));
    }
}
