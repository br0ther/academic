<?php

namespace BugTrackBundle\Entity\Repository;

use BugTrackBundle\Entity\ActivityListableInterface;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Project;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * IssueActivityRepository
 */
class IssueActivityRepository extends EntityRepository
{
    /**
     * @param ActivityListableInterface $object
     * @return IssueActivity[]
     */
    public function getActivitiesForEntity(ActivityListableInterface $object)
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.issue', 'i')
            ->orderBy('a.created', 'DESC');

        if ($object instanceof Project) {
            $qb->innerJoin('i.project', 'p')
                ->where('p.id = :projectId')
                ->setParameters([
                    'projectId' => $object->getId(),
                ]);
        } elseif ($object instanceof Issue) {
            $qb->where('i.id = :issueId')
                ->setParameters([
                    'issueId' => $object->getId(),
                ]);
        } elseif ($object instanceof User) {
            $qb->innerJoin('i.collaborators', 'u')
                ->where('u.id = :userId')
                ->setParameters([
                    'userId' => $object->getId(),
                ]);
        }

        return $qb->getQuery()->getResult();
    }
}
