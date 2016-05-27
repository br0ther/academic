<?php

namespace BugTrackBundle\Event;

use BugTrackBundle\Entity\Comment;
use Symfony\Component\EventDispatcher\Event;

class CommentEvent extends Event
{
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * IssueEvent constructor.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get Comment
     *
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
}
