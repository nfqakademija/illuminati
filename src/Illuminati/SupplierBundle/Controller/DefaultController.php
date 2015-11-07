<?php

namespace Illuminati\SupplierBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SupplierBundle:Default:index.html.twig', array('name' => 'supplier'));
    }
}
