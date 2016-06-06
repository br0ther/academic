<?php

namespace BugTrackBundle\Tests\Repository;


use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\IssueActivity;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Event\IssueEvent;
use BugTrackBundle\EventListener\IssueEventListener;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class IssueActivityRepositoryTest
 * @package BugTrackBundle\Tests\Repository
 */
class IssueActivityRepositoryTest extends WebTestCase
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
     * @covers \BugTrackBundle\Entity\Repository\IssueActivityRepository::getActivitiesForEntity()
     */
    public function testGetActivitiesForEntity()
    {
        /** @var User $userOperator2 */
        $userOperator2 = $this->fixtures->getReference('userOperator2');

        /** @var User $projectA */
        $projectA = $this->fixtures->getReference('projectA');
        
        /** @var Issue $issueResolved */
        $issueResolved = $this->fixtures->getReference('issueResolved');

        /** @var Issue $issueSubTask */
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $this->loadCollaboratorsOnIssue($issueResolved);
        
        /** @var IssueActivity $activities */
        $activities = $this->em
            ->getRepository('BugTrackBundle:IssueActivity')
            ->getActivitiesForEntity($userOperator2);
        
        $this->assertEquals(1, count($activities));
        $this->assertTrue($activities[0] instanceof IssueActivity);

        $activities = $this->em
            ->getRepository('BugTrackBundle:IssueActivity')
            ->getActivitiesForEntity($projectA);

        $this->assertEquals(5, count($activities));

        $activities = $this->em
            ->getRepository('BugTrackBundle:IssueActivity')
            ->getActivitiesForEntity($issueSubTask);

        $this->assertEquals(3, count($activities));
    }
    
    public function loadCollaboratorsOnIssue($issue)
    {
        $issueEventListener = new IssueEventListener();

        // filling collaborators
        $issueEvent = new IssueEvent($issue);
        $issueEventListener->issueCollaboratorsCheck($issueEvent);

        $this->em->flush();
    }
}
