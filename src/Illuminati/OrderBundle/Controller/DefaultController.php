<?php

namespace Illuminati\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Illuminati\OrderBundle\Form\Host_orderType;
use Illuminati\OrderBundle\Entity\Host_order;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->redirectToRoute("newHostOrder");
    }


    public function createAction(Request $request)
    {
        $hostOrder = new Host_order();

        //Prefilling the form with some data
        //Assigning hostOrder to the user
        $hostOrder->setUsersId($this->get("security.token_storage")->getToken()->getUser());
        $hostOrder->setCloseDate(new \DateTime("now"));

        $form = $this->createForm(new Host_orderType,$hostOrder);

        $form->handleRequest($request);

        if($form->isValid())
        {

            $em = $this->getDoctrine()->getManager();

            // Getting default orderState for the order
            $stateId = $em->getRepository("IlluminatiOrderBundle:Host_order_state")->find(1);
            $hostOrder->setStateId($stateId);

            $em->persist($hostOrder);
            $em->flush();

            $orderParticipantsEmails = $request->request->get("illuminati_orderbundle_host_order")["orderPatricipants"];

            $orderParticipantsEmails = array_map("trim",explode(",",$orderParticipantsEmails));
            $listGenerator = $this->get("invite_generator");
            $listGenerator->setHostOrder($hostOrder);
            $listGenerator->setParticipantsEmailsArray($orderParticipantsEmails);
            $listGenerator->generate();
            $listGenerator->flush();
        }

        return $this->render("IlluminatiOrderBundle:Default:orderCreation.html.twig",["form"=>$form->createView()]);
    }
}