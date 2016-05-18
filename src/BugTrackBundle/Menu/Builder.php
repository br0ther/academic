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

        $menu->addChild('Home', array('route' => 'homepage'));

        $token = $this->container->get('security.token_storage')->getToken();
        $userName = $token->getUsername();

        $menu->addChild($userName, array(
            'route' => 'fos_user_profile_show',
            'attributes' => array(
                'class' => 'user-name'
            )
        ));

        $menu->addChild('Logout', array(
            'route' => 'fos_user_security_logout',
            'attributes' => array(
                'class' => 'user-name'
            )
        ));

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
            'myFeed' => array(
                'feed.*',
                'project_group_join_by_code',
            ),
            'library' => array(
                'library.*',
                'folder_list',
                'folder_project_list',
            ),
            'groups' => array(
                'project_group_.*',
                'group_project_.*',
                'organization_group_show',
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
