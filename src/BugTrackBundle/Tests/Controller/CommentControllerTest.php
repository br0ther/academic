<?php

namespace BugTrackBundle\Tests\Controller;

use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CommentControllerTest
 * @package BugTrackBundle\Tests\Controller
 */
class CommentControllerTest extends WebTestCase
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
            '\BugTrackBundle\DataFixtures\ORM\CommentsData',
        ])->getReferenceRepository();
    }

    /**
     * Test ViewPage
     */
    public function testViewPage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');

        $issueSubTask = $this->fixtures->getReference('issueSubTask');
        
        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/issue/view/' . $issueSubTask->getId());
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Comments',
            $content
        );

        $this->assertContains(
            'Created',
            $content
        );

        $this->assertContains(
            'Body',
            $content
        );

        $this->assertContains(
            'Actions',
            $content
        );

        $this->assertContains(
            'You might want to examine the return value ',
            $content
        );

        $this->assertContains(
            'For the major quirky types/values is_null(var) obviously always',
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
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $crawler = $loggedClient->request('GET', sprintf('/issue/%s/comment/create/', $issueSubTask->getId()));
        $this->assertEquals(1, $crawler->selectButton('Save')->count());
        $this->assertEquals(1, $crawler->selectLink('Cancel')->count());
        $this->assertEquals(
            1,
            $crawler->filterXPath('//*[@id="comment_form_issue"]/option[@value="2" and @selected="selected"]')
                ->count()
        );
        $this->assertEquals(
            1,
            $crawler->filterXPath('//*[@id="comment_form_author"]/option[@value="1" and @selected="selected"]')
                ->count()
        );

        $form = $crawler->filter('form[name=comment_form]')->form();
        
        $content = $loggedClient->getResponse()->getContent();

        $data = [
            'comment_form[body]' => 'Just another created comment',
        ];
        
        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/issue/view/' . $issueSubTask->getId())
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Just another created comment',
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
        $comment2 = $this->fixtures->getReference('comment2');
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/comment/edit/' . $comment2->getId());
        $form = $crawler->filter('form[name=comment_form]')->form();

        $data = [
            'comment_form[body]' => 'Just changed comment',
        ];

        $crawler = $loggedClient->submit($form, $data);

        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/issue/view/' . $issueSubTask->getId())
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Just changed comment',
            $content
        );
    }

    /**
     * Test DeletePage
     */
    public function testDeletePage()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        $comment1 = $this->fixtures->getReference('comment1');
        $issueSubTask = $this->fixtures->getReference('issueSubTask');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/comment/delete/' . $comment1->getId());


        $this->assertTrue(
            $loggedClient->getResponse()->isRedirect('/issue/view/' . $issueSubTask->getId())
        );

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'You might want to examine the return value ',
            $content
        );

        $this->assertNotContains(
            'For the major quirky types/values is_null(var) obviously always',
            $content
        );
    }
}
