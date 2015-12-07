<?php

namespace Illuminati\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $img = $this
            ->get('templating.name_parser')
            ->parse('@IlluminatiMainBundle/Resources/public/images/burger.pngs')
            ->getPath();

        return $this->render('IlluminatiMainBundle:Default:index.html.twig', array(
            'img' => $img
        ));
    }
}
