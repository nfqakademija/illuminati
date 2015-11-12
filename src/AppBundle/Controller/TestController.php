<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/contacts", name="test")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder(array())
            ->add('save', 'submit', array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            die('5');
        }

        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:form.html.twig', array(
            'test_form' => $form->createView(),
        ));
    }
}
