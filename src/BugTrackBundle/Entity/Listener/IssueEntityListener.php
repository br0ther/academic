<?php


namespace BugTrackBundle\Entity\Listener;

use BugTrackBundle\DBAL\Type\ActivityType;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Issue;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class IssueEntityListener
{
    const STATUS_FIELD = 'status';

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
     * @param Issue $issue
     * 
     * @return User|null
     */
    public function getIssueUser($issue)
    {
        if ($this->tokenStorage->getToken()) {
            return $this->tokenStorage->getToken()->getUser();
        } else {
            return $issue->getReporter();
        }
    }
    
    /**
     * @param Issue $issue
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Issue $issue, LifecycleEventArgs $event)
    {
        if ($user = $this->getIssueUser($issue)) {
            $activity = new IssueActivity();
            $activity->setType(ActivityType::ACTIVITY_ISSUE_ADDED);
            $activity->setIssue($issue);
            $activity->setUser($user);

            $em = $event->getEntityManager();
            $em->persist($activity);
            $em->flush($activity);
        }
    }

    /**
     * @param Issue $issue
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(Issue $issue, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField(self::STATUS_FIELD)) {
            $oldStatus = $event->getOldValue(self::STATUS_FIELD);
            $newStatus = $event->getNewValue(self::STATUS_FIELD);

            $activity = new IssueActivity();
            $activity->setType(ActivityType::ACTIVITY_ISSUE_CHANGED);
            $activity->setIssue($issue);
            $activity->setUser($this->getIssueUser($issue));
            $activity->setOldValue($oldStatus);
            $activity->setNewValue($newStatus);
            $activity->setChangedField(self::STATUS_FIELD);

            $this->activities[] = $activity;
        }
    }

    /**
     * @param Issue $issue
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(Issue $issue, LifecycleEventArgs $event)
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
