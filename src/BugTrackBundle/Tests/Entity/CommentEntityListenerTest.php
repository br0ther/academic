<?php

namespace BugTrackBundle\Tests\Entity;

use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Listener\CommentEntityListener;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class CommentEntityListenerTest
 * @package BugTrackBundle\Tests\Entity
 */
class CommentEntityListenerTest extends \PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $issue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $comment;
    
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $tokenStorage;

    /**
     * @var commentEntityListener;
     */
    protected $commentEntityListener;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->issue = $this->getMock(Issue::class);
        $this->comment = $this->getMock(Comment::class);
        $this->tokenStorage = $this->getMock(TokenStorage::class);
        $this->commentEntityListener = new CommentEntityListener($this->tokenStorage);
    }

    /**
     * testPostPersist
     */
    public function testPostPersist()
    {
        $user = $this->getMock(User::class);
        $this->comment->method('getAuthor')->willReturn($user);
        $this->comment->method('getIssue')->willReturn($this->issue);

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $eventArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventArgs->method('getEntityManager')->willReturn($entityManager);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(IssueActivity::class));

        $entityManager->expects($this->once())
            ->method('flush')
            ->with($this->isInstanceOf(IssueActivity::class));

        $this->commentEntityListener->postPersist($this->comment, $eventArgs);
    }

    /**
     * testPreUpdate
     */
    public function testPreUpdate()
    {
        $eventArgs = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventArgs->method('hasChangedField')
            ->willReturn(CommentEntityListener::BODY_FIELD);

        $commentEntityListenerReflection = new \ReflectionClass(CommentEntityListener::class);
        $property = $commentEntityListenerReflection->getProperty('activities');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($this->commentEntityListener) === []);
        
        $this->commentEntityListener->preUpdate($this->comment, $eventArgs);
        
        $this->assertTrue($property->getValue($this->commentEntityListener)[0] instanceof IssueActivity);
    }

    /**
     * testPostUpdate
     */
    public function testPostUpdate()
    {
        $issueActivity = $this->getMock(IssueActivity::class);
        
        $eventArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventArgs->method('getEntityManager')->willReturn($entityManager);

        $commentEntityListenerReflection = new \ReflectionClass(CommentEntityListener::class);
        $property = $commentEntityListenerReflection->getProperty('activities');
        $property->setAccessible(true);
        $property->setValue($this->commentEntityListener, [$issueActivity]);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(IssueActivity::class));
        
        $this->assertTrue($property->getValue($this->commentEntityListener)[0] instanceof IssueActivity);

        $this->commentEntityListener->postUpdate($this->comment, $eventArgs);

    }
}
