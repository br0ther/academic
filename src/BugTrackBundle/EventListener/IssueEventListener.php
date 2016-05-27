<?php

namespace BugTrackBundle\EventListener;

use BugTrackBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use BugTrackBundle\Event\BugTrackEvents;
use BugTrackBundle\Event\IssueEvent;

class IssueEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

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

        $issue->addNotExistsCollaborator($issue->getReporter());
        $issue->addNotExistsCollaborator($issue->getAssignee());
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
        
        $issue->addNotExistsCollaborator($comment->getAuthor());
    }
}
