<?php

namespace BugTrackBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', ['navbar' => true]);

        $menu->addChild('Home', ['route' => 'homepage']);
        $menu->addChild('Projects', ['route' => 'homepage']);
        $menu->addChild('Issues', ['route' => 'homepage']);
        
        $active = false;
        foreach ($menu->getChildren() as $item) {
            /** @var ItemInterface $item */
            if ($item->isCurrent()) {
                $active = true;
                break;
            }
        }

        $route = $this->container->get('request')->attributes->get('_route');

        if (!$active) {
            $active = $this->activeMenuByRouteName($route);
            if ($active && isset($menu[$active])) {
                $menu[$active]->setCurrent(true);
            }
        }

        return $menu;
    }

    public function profileMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('profile', ['navbar-right' => true,]);

        $menu->addChild('Profile', [
            'route' => 'user_view',
            'attributes' => [
            ]
        ]);

        $menu->addChild('Logout', [
            'route' => 'fos_user_security_logout',
            'attributes' => [
            ]
        ]);

        $active = false;
        foreach ($menu->getChildren() as $item) {
            /** @var ItemInterface $item */
            if ($item->isCurrent()) {
                $active = true;
                break;
            }
        }

        $route = $this->container->get('request')->attributes->get('_route');

        if (!$active) {
            $active = $this->activeMenuByRouteName($route);
            if ($active && isset($menu[$active])) {
                $menu[$active]->setCurrent(true);
            }
        }

        return $menu;
    }

    /**
     * Find current route name in
     *
     * @param string $currentRoute
     *
     * @return bool|string
     */
    private function activeMenuByRouteName($currentRoute)
    {
        $routes = array(
            'Profile' => array(
                'user.*',
            ),
            'Projects' => array(
                'project.*',
            ),
            'Issues' => array(
                'issue.*',
            ),
        );

        foreach ($routes as $key => $route) {
            $regexp = sprintf('/^(%s)$/', implode('|', $route));

            if (preg_match($regexp, $currentRoute)) {
                return $key;
            }
        }

        return false;
    }
}
