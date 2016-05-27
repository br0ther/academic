<?php

namespace BugTrackBundle\Entity\Repository;

use BugTrackBundle\DBAL\Type\IssueType;
use BugTrackBundle\DBAL\Type\StatusType;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * IssueRepository
 */
class IssueRepository extends EntityRepository
{
    /**
     * Gets Issues with type story QueryBuilder
     *
     * @return QueryBuilder
     */
    public function getIssuesStoryQueryBuilder()
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.type = :type')
            ->setParameter('type', IssueType::TYPE_STORY)
            ->orderBy('i.id', 'DESC');
        
        return $qb;
    }

    /**
     * @param User $user
     * @return Issue[]
     */
    public function getIssuesForMainPage(User $user)
    {
        $qb = $this->createQueryBuilder('i')
            ->innerJoin('i.collaborators', 'u')
            ->where('u.id = :userId')
            ->andWhere('i.status IN(:status)')
            ->setParameters([
                'userId' => $user->getId(),
                'status' => [StatusType::STATUS_OPEN],
            ]);
        
        return $qb;
    }
}
