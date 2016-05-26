<?php

namespace BugTrackBundle\Tests\Controller;

use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ProjectControllerTest
 * @package BugTrackBundle\Tests\Controller
 */
class ProjectControllerTest extends WebTestCase
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
        ])->getReferenceRepository();
    }

    /**
     * Test ViewPage
     */
    public function testViewPage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        
        $projectA = $this->fixtures->getReference('projectA');
        
        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/project/view/' . $projectA->getId());
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Project A (ABC)',
            $content
        );

        $this->assertContains(
            'On the other hand,',
            $content
        );

        $this->assertContains(
            'Andy Manager, Andy Operator',
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

        $crawler = $loggedClient->request('GET', '/project/create');
        $form = $crawler->filter('form[name=project_form]')->form();

        $data = [
            'project_form[label]' => 'P',
            'project_form[code]' => 'ABC',
            'project_form[summary]' => 'Summary text',
            'project_form[members]' => ['1', '2'],
        ];
        $crawler = $loggedClient->submit($form, $data);
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'The label is too short.',
            $content
        );

        $this->assertContains(
            'Sorry, this code is already in use.',
            $content
        );

        $data = [
            'project_form[label]' => 'Project New',
            'project_form[code]' => 'ABCDE',
            'project_form[summary]' => 'Summary text',
            'project_form[members]' => ['1', '2'],
        ];
        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/project/view/3')
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Project New (ABCDE)',
            $content
        );

        $this->assertContains(
            'Summary text',
            $content
        );

        $this->assertContains(
            'Andy Admin, Andy Manager',
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
        $projectB = $this->fixtures->getReference('projectB');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/project/edit/' . $projectB->getId());
        $form = $crawler->filter('form[name=project_form]')->form();

        $data = [
            'project_form[label]' => 'Changed Project B',
            'project_form[code]' => 'CH_ABC',
            'project_form[summary]' => 'Changed Summary text',
            'project_form[members]' => ['1', '2'],
        ];
        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/project/view/' . $projectB->getId())
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Changed Project B (CH_ABC)',
            $content
        );

        $this->assertContains(
            'Changed Summary text',
            $content
        );

        $this->assertContains(
            'Andy Admin, Andy Manager',
            $content
        );
    }
}
