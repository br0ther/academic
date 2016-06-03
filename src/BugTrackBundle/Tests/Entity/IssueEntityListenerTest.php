<?php

namespace BugTrackBundle\Tests\Entity;

use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\Listener\IssueEntityListener;
use BugTrackBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class IssueEntityListenerTest extends \PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $issue;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $tokenStorage;

    /**
     * @var IssueEntityListener;
     */
    protected $issueEntityListener;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->issue = $this->getMock(Issue::class);
        $this->tokenStorage = $this->getMock(TokenStorage::class);
        $this->issueEntityListener = new IssueEntityListener($this->tokenStorage);
    }

    public function testPostPersist()
    {
        $user = $this->getMock(User::class);
        $this->issue->method('getReporter')->willReturn($user);

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

        $this->issueEntityListener->postPersist($this->issue, $eventArgs);
    }

    public function testPreUpdate()
    {
        $eventArgs = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventArgs->method('hasChangedField')
            ->willReturn(IssueEntityListener::STATUS_FIELD);

        $ssueEntityListenerReflection = new \ReflectionClass(IssueEntityListener::class);
        $property = $ssueEntityListenerReflection->getProperty('activities');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($this->issueEntityListener) === []);
        
        $this->issueEntityListener->preUpdate($this->issue, $eventArgs);
        
        $this->assertTrue($property->getValue($this->issueEntityListener)[0] instanceof IssueActivity);
    }
}
