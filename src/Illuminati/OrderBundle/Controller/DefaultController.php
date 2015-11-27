<?php

namespace Illuminati\OrderBundle\Controller;

use Illuminati\OrderBundle\Entity\User_order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminati\OrderBundle\Form\Host_orderType;
use Illuminati\OrderBundle\Entity\Host_order;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Redirect to new host order creation form
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirectToRoute("host_order_new");
    }

    /**
     * Host Order creation form
     * @param Request $request Submitted form request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $hostOrder = new Host_order();

        //Prefilling the form with some data
        //Assigning hostOrder to the user

        $userObject = $this->get("security.token_storage")->getToken()->getUser();

        $hostOrder->setUsersId($userObject);
        $hostOrder->setCloseDate(new \DateTime("now"));

        $form = $this->createForm(new Host_orderType, $hostOrder);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($hostOrder);

            // Creating User_order for the host user

            $userOrder = new User_order();
            $userOrder->setHostOrderId($hostOrder);
            $userOrder->setUsersId($userObject);
            $em->persist($userOrder);
            $em->flush();

            $notificationMessage = $this->get('translator')
                ->trans('order.summary.successCreate');

            $this->get('session')->getFlashBag()
                ->add('success', $notificationMessage);

            return $this->redirectToRoute(
                'host_order_summary',
                ['id' => $hostOrder->getId()]
            );
        }

        return $this->render(
            "IlluminatiOrderBundle:Default:orderCreation.html.twig",
            ["form" => $form->createView(), 'pageTitle' => 'order.create']
        );
    }

    /**
     * Host order summary
     * @param string $id Host order id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *         \Symfony\Component\HttpFoundation\Response
     */
    public function summaryAction($id)
    {
        if (($hostOrderObj = $this->get('host_order_participation_checker')->check((int)$id))) {
            //getting participants

            $em = $this->getDoctrine()->getManager()
                ->getRepository("IlluminatiOrderBundle:Host_order");

            $participants = $em->findUserOrders($id);
            $participantsOrders = $em->findUsersOrderDetails($id);

            return $this->render(
                "IlluminatiOrderBundle:Default/Summary:base.html.twig",
                [
                    'hostOrder'          => $hostOrderObj,
                    'participants'       => $participants,
                    'participantsOrders' => $participantsOrders
                ]
            );
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Host order edit page
     *
     * @param Request $request Submitted form request
     * @param integer $id      Host order id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *          \Symfony\Component\HttpFoundation\Response
     */
    public function editHostOrderAction(Request $request, $id)
    {
        if (($hostOrder = $this->get('host_order_host_checker')->check((int)$id))) {
            if ($hostOrder->getStateId() != 1) {
                $notificationMessage = $this
                    ->get('translator')
                    ->trans("notify.messages.warning.cantEditClosedOrder");
                $this->get('session')->getFlashBag()->add('info', $notificationMessage);

                return $this->redirectToRoute('host_order_summary', ['id'=>$hostOrder->getId()]);
            }

            $em = $this
                ->getDoctrine()
                ->getManager();

            $form = $this->createForm(new Host_orderType, $hostOrder);

            // changing submit button label
            $form->remove('submit');
            $form->add('submit', 'submit', array("label" => "order.update"));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                $notificationMessage = $this->get('translator')
                    ->trans('order.summary.successUpdate');

                $this->get('session')->getFlashBag()
                    ->add('success', $notificationMessage);

                return $this->redirectToRoute(
                    'host_order_summary',
                    ['id' => $hostOrder->getId()]
                );
            }

            return $this->render(
                "IlluminatiOrderBundle:Default:orderCreation.html.twig",
                ["form" => $form->createView(), 'pageTitle' => 'order.edit']
            );
        } else {
            return $this->redirectToRoute('homepage');
        }

    }

    /**
     * Sends reminder emails to order participants who owe money
     *
     * @param integer $id Host order id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function debtReminderAction($id)
    {
        if (($hostOrder = $this->get('host_order_host_checker')->check((int)$id))) {
            $em = $this->getDoctrine()->getManager();
            $debtors = $em
                ->getRepository('IlluminatiOrderBundle:Host_order')
                ->findOrderDebtors($id);

            $emailSentCount = 0;

            foreach ($debtors as $debtor) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Group order product payment reminder')
                    ->setTo($debtor->getEmail())
                    ->setBody(
                        $this->renderView(
                            'IlluminatiOrderBundle:Emails:debtEmail.html.twig',
                            [
                                'hostOrder' => $hostOrder,
                                'debtor'    => $debtor
                            ]
                        ),
                        'text/html'
                    );

                $this->get('mailer')->send($message);

                $emailSentCount++;
            }

            $notificationMessage = $this->get('translator')->trans(
                'order.summary.successEmailSent_%count%',
                ['%count%' => $emailSentCount]
            );

            $this->get('session')->getFlashBag()
                ->add('success', $notificationMessage);

            return $this->redirectToRoute(
                "host_order_summary",
                ['id' => $hostOrder->getId()]
            );

        } else {
            return $this->redirectToRoute('homepage');
        }

    }

    /**
     * Shows Order History
     * @param $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showHistoryAction($type)
    {
        $userId = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        if ($type === 'hosted') {
            $orders = $em->getRepository('IlluminatiOrderBundle:Host_order')
                ->findHostedOrders($userId);

            return $this->render('IlluminatiOrderBundle:Default/History:history.html.twig', array(
                'orders'=>$orders,
                'type'=>$type,
            ));
        } elseif ($type === 'joined') {
            $orders = $em->getRepository('IlluminatiOrderBundle:Host_order')
                ->findJoinedOrders($userId);

            return $this->render('IlluminatiOrderBundle:Default/History:history.html.twig', array(
                'orders'=>$orders,
                'type'=>$type,
            ));
        }
    }

    /**
     * Host order joining
     *
     * @param string $hostOrderToken Host order join token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function joinOrderAction($hostOrderToken)
    {
        $hostOrder = $this->get('host_order_join_checker')->check($hostOrderToken);

        if (is_object($hostOrder)) {
            // Joining user to the host order

            $userOrder = new User_order();
            $userOrder->setHostOrderId($hostOrder);
            $userOrder->setUsersId(
                $this->get('security.token_storage')->getToken()->getUser()
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($userOrder);
            $em->flush();

            $notificationMessage = $this->get('translator')
                ->trans('order.summary.successJoined');

            $this->get('session')->getFlashBag()
                ->add('success', $notificationMessage);

            return $this->redirectToRoute(
                'host_order_summary',
                ['id'=>$hostOrder->getId()]
            );

        } elseif (is_int($hostOrder)) {
            // user already participates in the order

            $notificationMessage = $this->get('translator')
                ->trans('order.summary.infoAlreadyParticipates');

            $this->get('session')->getFlashBag()
                ->add('info', $notificationMessage);

            return $this->redirectToRoute(
                'host_order_summary',
                ['id'=>$hostOrder]
            );

        } else {
            return $this->redirectToRoute('homepage');

        }
    }

    /**
     * Leaving group order
     *
     * @param integer $id Host Order id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function leaveOrderAction($id)
    {
        $hostOrder = $this->get('host_order_participation_checker')->check((int)$id);

        if (is_object($hostOrder)) {
            /*
                Checking if the host order is closed.
                If it is, don't allow to leave it.
             */
            if ($hostOrder->getStateId() != 1) {
                $notificationMessage = $this
                    ->get('translator')
                    ->trans('notify.messages.warning.cantLeaveClosedOrder');

                $this->get('session')->getFlashBag()->add('info', $notificationMessage);

                return $this->redirectToRoute(
                    'host_order_summary',
                    ['id'=>$hostOrder->getId()]
                );
            }

            $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

            $deletedParticipant = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('IlluminatiOrderBundle:Host_order')
                ->deleteParticipant($hostOrder, $userId);

            if ($deletedParticipant) {
                $notificationMessage = $this
                    ->get('translator')
                    ->trans('notify.messages.success.orderLeft');

                $this->get('session')->getFlashBag()->add('success', $notificationMessage);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * Order confirmation page
     *
     * @param string $id Host order id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *         \Symfony\Component\HttpFoundation\Response
     */
    public function hostOrderConfirmationAction($id)
    {
        if (($hostOrder = $this->get('host_order_host_checker')->check((int)$id))) {
            if ($hostOrder->getStateId() != 1) {
                return $this->redirectToRoute('host_order_summary', ['id'=>$hostOrder->getId()]);
            }

            $products = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('IlluminatiOrderBundle:Host_order')
                ->findOrderedProducts($hostOrder->getId());

            return $this->render(
                "IlluminatiOrderBundle:Default/OrderConfirmation:base.html.twig",
                ['products' => $products, 'orderId' => $hostOrder->getId()]
            );
        } else {
            return $this->redirectToRoute('host_order_summary', ['id'=>$hostOrder->getId()]);
        }
    }

    /**
     * Confirms Host order and marks as closed.
     *
     * @param string $id Host order id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|
     *         \Symfony\Component\HttpFoundation\Response
     */
    public function hostOrderConfirmedAction($id)
    {
        if (($hostOrder = $this->get('host_order_host_checker')->check((int)$id))) {
            if ($hostOrder->getStateId() != 1) {
                return $this->redirectToRoute('host_order_summary', ['id'=>$hostOrder->getId()]);
            }

            $em = $this->getDoctrine()->getManager();

            // Confirming the order
            $hostOrder->setStateId(2);
            $em->flush();

            $orderParticipants = $em
                ->getRepository('IlluminatiOrderBundle:Host_order')
                ->findUserOrders($hostOrder->getId());

            // Sending emails about confirmed order to participants
            foreach ($orderParticipants as $participant) {
                $message = \Swift_Message::newInstance()
                    ->setSubject("Group order - {$hostOrder->getTitle()} - has been confirmed!")
                    ->setTo($participant->getUsersId()->getEmail())
                    ->setBody(
                        $this->renderView(
                            'IlluminatiOrderBundle:Emails:orderConfirmedEmail.html.twig',
                            ['hostOrder'=>$hostOrder,'usersName'=>$participant->getUsersId()]
                        ),
                        'text/html'
                    );

                $this->get('mailer')->send($message);
            }

            $notificationMessage = $this->get('translator')->trans('order.summary.confirmation.confirmedSuccess');
            $this->get('session')->getFlashBag()->add('success', $notificationMessage);

            return $this->redirectToRoute('host_order_summary', ['id'=>$hostOrder->getId()]);
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Generates pdf with hosted order's products and participants
     *
     * @param string $id Host Order id;
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function genPdfAction($id)
    {
        if (($hostOrder = $this->get('host_order_host_checker')->check((int)$id))) {
            $pdf = $this->get('host_order_products_pdf_generator');
            $pdf->generate($hostOrder);

            return new Response($pdf->Output(), 200, array(
                'Content-Type' => 'application/pdf'));
        } else {
            return $this->redirectToRoute('homepage');
        }
    }
}
