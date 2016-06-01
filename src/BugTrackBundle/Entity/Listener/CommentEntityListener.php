<?php


namespace BugTrackBundle\Entity\Listener;

use BugTrackBundle\DBAL\Type\ActivityType;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CommentEntityListener
{
    const BODY_FIELD = 'body';

    protected $activities = [];
    
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
    
    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Comment $comment
     * 
     * @return User|null
     */
    public function getCommentUser($comment)
    {
        if ($this->tokenStorage->getToken()) {
            return $this->tokenStorage->getToken()->getUser();
        } else {
            return $comment->getAuthor();
        }
    }
    
    /**
     * @param Comment $comment
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Comment $comment, LifecycleEventArgs $event)
    {
        if ($user = $this->getCommentUser($comment)) {
            $activity = new IssueActivity();
            $activity->setType(ActivityType::ACTIVITY_COMMENT_ADDED);
            $activity->setIssue($comment->getIssue());
            $activity->setUser($user);
            $activity->setComment($comment);

            $em = $event->getEntityManager();
            $em->persist($activity);
            $em->flush($activity);
        }
    }

    /**
     * @param Comment $comment
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Comment $comment, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField(self::BODY_FIELD)) {
            $oldValue = $event->getOldValue(self::BODY_FIELD);
            $newValue = $event->getNewValue(self::BODY_FIELD);

            $activity = new IssueActivity();
            $activity->setType(ActivityType::ACTIVITY_COMMENT_CHANGED);
            $activity->setIssue($comment->getIssue());
            $activity->setUser($this->getCommentUser($comment));
            $activity->setComment($comment);
            $activity->setOldValue($oldValue);
            $activity->setNewValue($newValue);
            $activity->setChangedField(self::BODY_FIELD);

            $this->activities[] = $activity;
        }
    }

    /**
     * @param Comment $comment
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Comment $comment, LifecycleEventArgs $event)
    {
        if (!empty($this->activities)) {
            $em = $event->getEntityManager();

            foreach ($this->activities as $activity) {
                $em->persist($activity);
            }

            $this->activities = [];
            $em->flush();
        }
    }
}
