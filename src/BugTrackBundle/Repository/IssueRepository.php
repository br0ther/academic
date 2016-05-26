<?php

namespace BugTrackBundle\Repository;

use BugTrackBundle\DBAL\Type\IssueType;
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
}
