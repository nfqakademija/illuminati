<?php
// src/AppBundle/Menu/Builder.php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        // $request = $this->container->get('request');

        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right',
            ),
        ));

        $menu->addChild('Home', array('route' => 'homepage'));

        /*
        $menu->addChild('Products', [
            'route' => 'product',
            'routeParameters' => [
                'order_id' => $request->get('order_id')
            ]
        ]);
        */

        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {

            $menu->addChild('UserLogin', array(
                'route' => 'fos_user_security_login',
                'label' => 'Login'
            ));

            $menu->addChild('UserRegister', array(
                'route' => 'fos_user_registration_register',
                'label' => 'Register'
            ));

        } else {

            $menu->addChild('NewHostOrder', array(
                'route' => 'host_order_new',
                'label' => 'New host order'
            ));

            $menu->addChild('UserProfile', array(
                'route' => 'fos_user_profile_edit',
                'label' => 'Profile'
            ));

            $menu->addChild('UserLogout', array(
                'route' => 'fos_user_security_logout',
                'label' => 'Logout'
            ));
        }

        return $menu;
    }
}