<?php

namespace BugTrackBundle\Tests\Menu;


use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class BuilderTest extends WebTestCase
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
        ])->getReferenceRepository();
    }

    public function testMainMenu()
    {

        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');

        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/');

        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Home',
            $content
        );

        $this->assertContains(
            'Projects',
            $content
        );

        $this->assertContains(
            'My Opened Issues',
            $content
        );

        $this->assertContains(
            'My Activities',
            $content
        );
    }
}
