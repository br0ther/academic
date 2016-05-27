<?php

namespace BugTrackBundle\Event;

use BugTrackBundle\Entity\Issue;
use Symfony\Component\EventDispatcher\Event;

class IssueEvent extends Event
{
    /**
     * @var Issue
     */
    protected $issue;

    /**
     * IssueEvent constructor.
     *
     * @param Issue $issue
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Get Issue
     *
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}
