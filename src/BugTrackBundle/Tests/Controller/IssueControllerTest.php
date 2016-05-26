<?php

namespace BugTrackBundle\Tests\Controller;

use BugTrackBundle\DBAL\Type\IssueType;
use BugTrackBundle\DBAL\Type\PriorityType;
use BugTrackBundle\DBAL\Type\ResolutionType;
use BugTrackBundle\DBAL\Type\StatusType;
use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class IssueControllerTest
 * @package BugTrackBundle\Tests\Controller
 */
class IssueControllerTest extends WebTestCase
{
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
        ])->getReferenceRepository();
    }

    /**
     * Test ViewPage
     */
    public function testViewPage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');

        $issueTask = $this->fixtures->getReference('issueStory');
        $issueSubTask = $this->fixtures->getReference('issueSubTask');
        
        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/issue/view/' . $issueTask->getId());
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            '[ISS-01] Issue templating',
            $content
        );

        $this->assertContains(
            'Project A (ABC)',
            $content
        );

        $this->assertContains(
            'Story',
            $content
        );

        $this->assertContains(
            'Open',
            $content
        );

        $this->assertContains(
            'Major',
            $content
        );

        $this->assertContains(
            'Andy Operator',
            $content
        );

        $this->assertContains(
            'When opening new issues it would help guiding',
            $content
        );

        $this->assertNotContains(
            'Resolution',
            $content
        );

        $this->assertContains(
            'Subtasks',
            $content
        );

        $this->assertContains(
            '[ISS-02] Issue change templating',
            $content
        );

        $this->assertContains(
            '[ISS-03] Wrong parsing links in Description page',
            $content
        );

        $crawler = $loggedClient->request('GET', '/issue/view/' . $issueSubTask->getId());
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            '[ISS-02] Issue change templating',
            $content
        );
        
        $this->assertContains(
            '[ISS-01] Issue templating',
            $content
        );
    }

    /**
     * Test CreatePage
     */
    public function testCreatePage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/issue/create');
        $form = $crawler->filter('form[name=issue_form]')->form();

        $data = [
            'issue_form[project]' => 1,
            'issue_form[code]' => 'ABC',
            'issue_form[type]' => IssueType::TYPE_SUBTASK,
            'issue_form[status]' => StatusType::STATUS_RESOLVED,
            'issue_form[priority]' => PriorityType::PRIORITY_MINOR,
            'issue_form[summary]' => 'Bug summary',
            'issue_form[reporter]' => 1,
            'issue_form[assignee]' => 1,
        ];
        $crawler = $loggedClient->submit($form, $data);
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Type Subtask should provide with Parent issue',
            $content
        );

        $this->assertContains(
            'Resolution should provide with status Resolved',
            $content
        );

        $data = [
            'issue_form[project]' => 1,
            'issue_form[code]' => 'ISS-04',
            'issue_form[type]' => IssueType::TYPE_SUBTASK,
            'issue_form[status]' => StatusType::STATUS_RESOLVED,
            'issue_form[priority]' => PriorityType::PRIORITY_MINOR,
            'issue_form[summary]' => 'Subtask feature',
            'issue_form[reporter]' => 1,
            'issue_form[assignee]' => 1,
            'issue_form[parentIssue]' => 1,
            'issue_form[resolution]' => ResolutionType::RESOLUTION_FIXED,
            'issue_form[description]' => 'We need to create flow for subtasks',
        ];
        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/issue/view/4')
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            '[ISS-04] Subtask feature',
            $content
        );

        $this->assertContains(
            '[ISS-01] Issue templating',
            $content
        );

        $this->assertContains(
            'Project A (ABC)',
            $content
        );

        $this->assertContains(
            'Subtask',
            $content
        );

        $this->assertContains(
            'Resolved',
            $content
        );

        $this->assertContains(
            'Fixed',
            $content
        );

        $this->assertContains(
            'Andy Admin',
            $content
        );

        $this->assertContains(
            'We need to create flow for subtasks',
            $content
        );
    }

    /**
     * Test EditPage
     */
    public function testEditPage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        $issue = $this->fixtures->getReference('issueSubTask');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/issue/edit/' . $issue->getId());
        $form = $crawler->filter('form[name=issue_form]')->form();

        $data = [
            'issue_form[status]' => StatusType::STATUS_CLOSED,
            'issue_form[assignee]' => 2,
            'issue_form[description]' => 'We don\'t need to create new templating',
        ];
        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/issue/view/' . $issue->getId())
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Closed',
            $content
        );

        $this->assertContains(
            'We don&#039;t need to create new templating',
            $content
        );

        $this->assertContains(
            'Andy Manager',
            $content
        );

        $this->assertContains(
            'Andy Operator',
            $content
        );

    }
}
