<?php

namespace BugTrackBundle\Tests\EventListener;

use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Event\CommentEvent;
use BugTrackBundle\Event\IssueEvent;
use BugTrackBundle\EventListener\IssueEventListener;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class IssueEventListenerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    protected $fixtures;

    /**
     * @var IssueEventListener
     */
    protected $issueEventListener;
    
    /**
     * SetUp
     */
    public function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            '\BugTrackBundle\DataFixtures\ORM\UsersData',
            '\BugTrackBundle\DataFixtures\ORM\ProjectsData',
            '\BugTrackBundle\DataFixtures\ORM\IssuesData',
            '\BugTrackBundle\DataFixtures\ORM\CommentsData',
        ])->getReferenceRepository();

        $this->issueEventListener = new IssueEventListener();
    }

    /**
     * Test BugTrackEvents::ISSUE_COLLABORATORS_CHECK
     */
    public function testIssueCollaboratorsCheck()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        
        /** @var Issue $issueResolved */
        $issueResolved = $this->fixtures->getReference('issueResolved');

        $this->assertEquals(0, $issueResolved->getCollaborators()->count());

        $issueEvent = new IssueEvent($issueResolved);
        $this->issueEventListener->issueCollaboratorsCheck($issueEvent);

        $this->assertEquals(1, $issueResolved->getCollaborators()->count());
        
        $issueResolved->setReporter($userAdmin);
        $this->issueEventListener->issueCollaboratorsCheck($issueEvent);
        
        $this->assertEquals(2, $issueResolved->getCollaborators()->count());

        $issueResolved->setAssignee($userAdmin);
        $this->issueEventListener->issueCollaboratorsCheck($issueEvent);

        $this->assertEquals(2, $issueResolved->getCollaborators()->count());

    }

    /**
     * Test BugTrackEvents::COMMENT_COLLABORATORS_CHECK
     */
    public function testCommentCollaboratorsCheck()
    {
        /** @var Issue $issueSubTask */
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $this->assertEquals(0, $issueSubTask->getCollaborators()->count());
        
        /** @var Comment $comment1 */
        $comment1 = $this->fixtures->getReference('comment1');
        /** @var Comment $comment2 */
        $comment2 = $this->fixtures->getReference('comment2');

        $commentEvent = new CommentEvent($comment1);
        $this->issueEventListener->commentCollaboratorsCheck($commentEvent);

        $this->assertEquals(1, $issueSubTask->getCollaborators()->count());

        $commentEvent = new CommentEvent($comment2);
        $this->issueEventListener->commentCollaboratorsCheck($commentEvent);

        $this->assertEquals(2, $issueSubTask->getCollaborators()->count());
        
        $commentEvent = new CommentEvent($comment2);
        $this->issueEventListener->commentCollaboratorsCheck($commentEvent);

        $this->assertEquals(2, $issueSubTask->getCollaborators()->count());
    }
}
