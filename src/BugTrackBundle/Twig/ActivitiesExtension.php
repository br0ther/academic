<?php

namespace BugTrackBundle\Twig;

use BugTrackBundle\Entity\IssueActivity;
use Doctrine\ORM\EntityManager;
use BugTrackBundle\Entity\Project;

class ActivitiesExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Constructor.
     *
     * @param EntityManager   $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return 'activities_extension';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_project_activities', [$this, 'getProjectActivities']),
            new \Twig_SimpleFunction('get_activity_title', [$this, 'getActivityTitle']),
        ];
    }

    /**
     * getProjectActivities
     *
     * @param Project $project
     * @return IssueActivity[]
     */
    public function getProjectActivities(Project $project)
    {
        return $this->em->getRepository('BugTrackBundle:IssueActivity')->getActivitiesForProject($project);
    }

    public function getActivityTitle(IssueActivity $activity)
    {
        return 'Activity Title';
    }
}
