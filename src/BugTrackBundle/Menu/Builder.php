<?php

namespace BugTrackBundle\Menu;

use BugTrackBundle\Security\Credential;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;


    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $authChecker = $this->container->get('security.authorization_checker');
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu = $factory->createItem('root', ['navbar' => true]);
        $menu->addChild('Home', ['route' => 'homepage']);

        //Projects dropdown
        $dropdown = $menu->addChild('Projects', [
            'dropdown' => true,
            'caret' => true,
        ]);
        $dropdown->addChild('Recent projects');

        $projects = $em->getRepository('BugTrackBundle:Project')->getUserRecentProjects($user, 5);

        foreach ($projects as $project) {
            $dropdown->addChild($project->getTitle(), [
                'route' => 'project_view',
                'routeParameters' => array('id' => $project->getId())
            ]);
        }

        if ($authChecker->isGranted(Credential::CREATE_PROJECT, $user)) {
            $dropdown->addChild('divider', ['divider' => true]);
            $dropdown->addChild('Create', [
                'route' => 'project_create',
            ]);
        }

        //Issues dropdown
        $dropdown = $menu->addChild('Issues', [
            'dropdown' => true,
            'caret' => true,
        ]);

        $dropdown->addChild('Recent issues');

        $issues = $em->getRepository('BugTrackBundle:Issue')->getUserRecentIssues($user, 5);

        foreach ($issues as $issue) {
            $dropdown->addChild($issue->getTitle(), [
                'route' => 'issue_view',
                'routeParameters' => array('id' => $issue->getId())
            ]);
        }

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
