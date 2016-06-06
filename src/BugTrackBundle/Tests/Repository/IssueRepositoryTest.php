<?php

namespace BugTrackBundle\Tests\Repository;


use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Event\IssueEvent;
use BugTrackBundle\EventListener\IssueEventListener;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class IssueRepositoryTest
 * @package BugTrackBundle\Tests\Repository
 */
class IssueRepositoryTest extends WebTestCase
{

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var ReferenceRepository
     */
    protected $fixtures;
    
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

        $this->em = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers \BugTrackBundle\Entity\Repository\IssueRepository::getIssuesStoryQB()
     */
    public function testGetIssuesStoryQB()
    {
        /** @var Issue $issues */
        $issues = $this->em
            ->getRepository('BugTrackBundle:Issue')
            ->getIssuesStoryQB()
            ->getQuery()
            ->getResult();
        
        $this->assertEquals(1, count($issues));
        $this->assertTrue($issues[0] instanceof Issue);
    }

    /**
     * @covers \BugTrackBundle\Entity\Repository\IssueRepository::getIssuesForMainPageQB()
     */
    public function testGetActivitiesForEntity()
    {
        /** @var User $userOperator */
        $userOperator = $this->fixtures->getReference('userOperator');

        /** @var Issue $issueSubTask */
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $this->loadCollaboratorsOnIssue($issueSubTask);

        $issues = $this->em
            ->getRepository('BugTrackBundle:Issue')
            ->getIssuesForMainPageQB($userOperator)
            ->getQuery()
            ->getResult();

        $this->assertEquals(1, count($issues));
        $this->assertTrue($issues[0] instanceof Issue);
    }

    public function loadCollaboratorsOnIssue($issue)
    {
        $issueEventListener = new IssueEventListener();

        $issueEvent = new IssueEvent($issue);
        $issueEventListener->issueCollaboratorsCheck($issueEvent);

        $this->em->flush();
    }
}
