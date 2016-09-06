<?php
namespace AppBundle\Menu;
/**
 * Created by PhpStorm.
 * User: dimas
 * Date: 14.07.15
 * Time: 20:20
 */

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * @param FactoryInterface $factory
     * @param array            $options
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('main');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('menu.home', array('route' => 'home_page'));
        $menu->addChild('menu.authors', array('route' => 'authors_list'));
        $menu->addChild('menu.composition_catgories', array('route' => 'category_list'));
        $menu->addChild('menu.search', array('route' => 'search_page'));
        $menu->addChild('menu.about_us', array('route' => 'about_us_page'));
        $menu->addChild('menu.contacts', array('route' => 'contacts_page'));

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array            $options
     * @return \Knp\Menu\ItemInterface
     */
    public function topMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('top');
        $menu->setChildrenAttribute('class', 'nav nav-pills');

        $menu->addChild('menu.help', array('route' => 'help_page'));
        $menu->addChild('menu.about_us', array('route' => 'about_us_page'));
        $menu->addChild('menu.donate', array('route' => 'donate_page'));
        $menu->addChild('menu.contacts', array('route' => 'contacts_page'));

        $menu->setCurrent(true);

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array            $options
     * @return \Knp\Menu\ItemInterface
     */
    public function userMenu(FactoryInterface $factory, array $options)
    {
        $isLogged = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');

        $menu = $factory->createItem('user');
        $menu->setChildrenAttribute('class', 'nav nav-pills pull-right');

        if ($isLogged) {
            $menu->addChild('menu.profile', array('route' => 'fos_user_profile_show'));
            $menu->addChild('menu.logout', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('menu.login', array('route' => 'fos_user_security_login'));
            $menu->addChild('menu.register', array('route' => 'fos_user_registration_register'));
        }

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @param array            $options
     * @return \Knp\Menu\ItemInterface
     */
    public function leftUserMenu(FactoryInterface $factory, array $options)
    {
        $isLogged = $this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY');

        $menu = $factory->createItem('user');
        $menu->setChildrenAttribute('class', 'nav nav-pills pull-right');

        if ($isLogged) {
            $menu->addChild('menu.profile', array('route' => 'fos_user_profile_show'));
            $menu->addChild('menu.logout', array('route' => 'fos_user_security_logout'));
        }

        return $menu;
    }

}