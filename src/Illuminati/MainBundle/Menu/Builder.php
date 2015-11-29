<?php
// src/AppBundle/Menu/Builder.php
namespace Illuminati\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right',
            ),
        ));

        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('Products', array('route' => 'product'));

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
                'route' => 'fos_user_profile_show',
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