<?php
// src/AppBundle/Menu/Builder.php
namespace Illuminati\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class Builder
 * @package Illuminati\MainBundle\Menu
 */
class Builder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes' => array(
                'class' => 'nav navbar-nav navbar-right',
            ),
        ));

        $menu->addChild('Home', array('route' => 'homepage'));

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

            $menu->addChild('Hosted orders', [
                'route' => 'order_history',
                'routeParameters' => [
                    'type' => 'hosted'
                ]
            ]);

            $menu->addChild('Joined orders', [
                'route' => 'order_history',
                'routeParameters' => [
                    'type' => 'joined'
                ]
            ]);

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