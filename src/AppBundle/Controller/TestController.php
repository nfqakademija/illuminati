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
        if(isset($_POST['submit'])) {
            echo '5';
            exit;
        }

        // replace this example code with whatever you need
        return $this->render('AppBundle:Default:form.html.twig', array(
        ));
    }
}
