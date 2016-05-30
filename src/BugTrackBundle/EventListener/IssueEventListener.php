<?php

namespace BugTrackBundle\EventListener;

use BugTrackBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use BugTrackBundle\Event\BugTrackEvents;
use BugTrackBundle\Event\IssueEvent;

class IssueEventListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BugTrackEvents::ISSUE_COLLABORATORS_CHECK => ['issueCollaboratorsCheck'],
            BugTrackEvents::COMMENT_COLLABORATORS_CHECK => ['commentCollaboratorsCheck'],
        ];
    }

    /**
     * Processes issueCollaboratorsCheck event.
     *
     * @param IssueEvent $event
     */
    public function issueCollaboratorsCheck(IssueEvent $event)
    {
        $issue = $event->getIssue();

        if ($issue->getReporter()) {
            $issue->addNotExistsCollaborator($issue->getReporter());
        }

        if ($issue->getAssignee()) {
            $issue->addNotExistsCollaborator($issue->getAssignee());
        }
    }
    
    /**
     * Processes commentCollaboratorsCheck event.
     *
     * @param CommentEvent $event
     */
    public function commentCollaboratorsCheck(CommentEvent $event)
    {
        $comment = $event->getComment();
        $issue = $comment->getIssue();

        if ($comment->getAuthor()) {
            $issue->addNotExistsCollaborator($comment->getAuthor());
        }
    }
}
