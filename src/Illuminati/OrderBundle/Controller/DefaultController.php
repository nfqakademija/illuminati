<?php

namespace Illuminati\OrderBundle\Controller;

use Illuminati\OrderBundle\Entity\User_order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminati\OrderBundle\Form\Host_orderType;
use Illuminati\OrderBundle\Entity\Host_order;

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
                'host_order_summary', ['id' => $hostOrder->getId()]
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
                    'host_order_summary', ['id' => $hostOrder->getId()]
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
    public function deptReminderAction($id)
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
                    ->setFrom("Team5@gmail.com")
                    ->setTo($debtor->getEmail())
                    ->setBody(
                        $this->renderView(
                            'IlluminatiOrderBundle:Emails:debtEmail.html.twig',
                            [
                                'hostOrder' => $hostOrder,
                                'debtor'    => $debtor
                            ]
                        ), 'text/html'
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
                "host_order_summary", ['id' => $hostOrder->getId()]
            );

        } else {
            return $this->redirectToRoute('homepage');
        }

    }


    public function showHistoryAction($type)
    {
        $userId = $this->container->get('security.context')->getToken()->getUser()->getId();
        if($type == 'hosted')
        {
            $sql="
            SELECT HO.title, HO.close_date, HOS.state, COUNT(UO.host_order_id) AS pCnt
            FROM host_order HO
            JOIN host_order_state HOS ON HO.state_id = HOS.id
            JOIN user_order UO ON HO.id = UO.host_order_id
            WHERE HO.users_id = {$userId}
            ";
            $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
            $stmt->execute();
            $orders = $stmt->fetchAll();
        }

        return $this->render('IlluminatiOrderBundle:Default/History:hosted.html.twig', array(
            'orders'=>$orders
        ));
    }
}