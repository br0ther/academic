<?php

namespace BugTrackBundle\Entity\Repository;

use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

/**
 * IssueActivityRepository
 */
class IssueActivityRepository extends EntityRepository
{
    /**
     * @param Project $project
     * @return IssueActivity[]
     */
    public function getActivitiesForProject($project)
    {
        $activities = $this->createQueryBuilder('a')
            ->innerJoin('a.issue', 'i')
            ->innerJoin('i.project', 'p')
            ->where('p.id = :projectId')
            ->setParameters([
                'projectId' => $project->getId(),
            ])
            ->orderBy('a.created', 'DESC')
            ->getQuery()
            ->getResult();

        return $activities;
    }
}
