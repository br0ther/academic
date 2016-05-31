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
    public function getIssuesStoryQB()
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.type = :type')
            ->setParameter('type', IssueType::TYPE_STORY)
            ->orderBy('i.id', 'DESC');
        
        return $qb;
    }

    /**
     * Gets Issues for main page
     *
     * @param User $user
     * 
     * @return QueryBuilder
     */
    public function getIssuesForMainPageQB(User $user)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.collaborators', 'u')
            ->where('u.id = :userId')
            ->andWhere('i.status IN(:status)')
            ->setParameters([
                'userId' => $user->getId(),
                'status' => [StatusType::STATUS_OPEN],
            ]);
        
        return $qb;
    }

    /**
     * Gets Issues for main page
     *
     * @param User $user
     * @param integer $cntIssues
     * 
     * @return Issue[]
     */
    public function getUserRecentIssues(User $user, $cntIssues)
    {
        $issues = $this->createQueryBuilder('i')
            ->select('i')
            ->innerJoin('i.collaborators', 'u')
            ->where('u.id = :userId')
            ->setParameters([
                'userId' => $user->getId(),
            ])
            ->orderBy('i.id', 'DESC')
            ->setMaxResults($cntIssues)
            ->getQuery()
            ->getResult();

        return $issues;
    }
}
