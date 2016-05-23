<?php

namespace BugTrackBundle\Tests\Controller;

use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

/**
 * Class UserControllerTest
 * @package BugTrackBundle\Tests\Controller
 */
class UserControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ReferenceRepository
     */
    protected $fixtures;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->client = $this->makeClient();

        $this->fixtures = $this->loadFixtures([
            '\BugTrackBundle\DataFixtures\ORM\LoadUserData',
        ])->getReferenceRepository();
    }
    
    /**
     * Test user login
     */
    public function testLoginPage()
    {

        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
        $this->assertEquals(1, $crawler->selectLink('Register')->count());
        $this->assertEquals(1, $crawler->selectButton('Log in')->count());

        $form = $crawler->filter('form[action="/login_check"]')->form();
        $data = [
            '_username' => 'fake',
            '_password' => 'pass',
        ];
        $crawler = $this->client->submit($form, $data);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();
        
        $this->assertContains(
            'Invalid credentials.',
            $this->client->getResponse()->getContent()
        );
        
        $data = [
            '_username' => 'manager',
            '_password' => '123456',
        ];
        $crawler = $this->client->submit($form, $data);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertContains(
            'Home',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Test user register
     *
     */
    public function testRegisterPage()
    {
        $crawler = $this->client->request('GET', '/register/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[name="fos_user_registration_form"]')->count());
        $this->assertEquals(1, $crawler->selectLink('Log in')->count());
        $this->assertEquals(1, $crawler->selectButton('Register')->count());

        $form = $crawler->filter('form[name="fos_user_registration_form"]')->form();

        $data = [
            'fos_user_registration_form[email]' => 'd@f',
            'fos_user_registration_form[username]' => '123456',
            'fos_user_registration_form[plainPassword][first]' => '123',
            'fos_user_registration_form[plainPassword][second]' => '1234',
        ];
        $crawler = $this->client->submit($form, $data);
        $content = $this->client->getResponse()->getContent();

        $this->assertContains(
            'The email is not valid',
            $content
        );
        $this->assertContains(
            'The entered passwords don&#039;t match',
            $content
        );

        $data = [
            'fos_user_registration_form[email]' => 'admin@test.com',
            'fos_user_registration_form[username]' => '123456',
            'fos_user_registration_form[plainPassword][first]' => '123',
            'fos_user_registration_form[plainPassword][second]' => '123',
        ];
        $crawler = $this->client->submit($form, $data);
        $content = $this->client->getResponse()->getContent();

        $this->assertContains(
            'The email is already used ',
            $content
        );

        $data = [
            'fos_user_registration_form[email]' => 'admin2@test.com',
            'fos_user_registration_form[username]' => 'admin2',
            'fos_user_registration_form[plainPassword][first]' => '123',
            'fos_user_registration_form[plainPassword][second]' => '123',
        ];
        $crawler = $this->client->submit($form, $data);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/register/confirmed')
        );
        $crawler = $this->client->followRedirect();
        $content = $this->client->getResponse()->getContent();
        $this->assertContains(
            'Congrats admin2, your account is now activated.',
            $content
        );
    }

    /**
     * Test user view
     */
    public function testUserView()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();

        $crawler = $loggedClient->request('GET', '/user/view/' . $userAdmin->getId());

        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
        $this->assertEquals(0, $crawler->filter('form')->count());
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'Andy Admin',
            $content
        );
        $this->assertContains(
            'Administrator',
            $content
        );
    }

    /**
     * Test user edit
     */
    public function testUserEdit()
    {
        /** @var User $userAdmin */
        $userAdmin = $this->fixtures->getReference('userAdmin');
        $this->loginAs($userAdmin, 'main');
        $loggedClient = static::makeClient();
        
        $crawler = $loggedClient->request('GET', '/user/edit/' . $userAdmin->getId());
        $this->assertEquals(1, $crawler->selectButton('save')->count());
        $this->assertEquals(1, $crawler->selectButton('cancel')->count());
        $this->assertEquals(
            1,
            $crawler->filterXPath('//*[@id="user_form_roles"]/option[@value="ROLE_ADMIN" and @selected="selected"]')
                ->count()
        );
        $form = $crawler->filter('form[name=user_form]')->form();
        
        $data = [
            'user_form[email]' => 'changed_admin@test.com',
            'user_form[username]' => 'changed_name',
            'user_form[fullName]' => 'Changed full name',
            'user_form[roles]' => ['ROLE_MANAGER', 'ROLE_ADMIN'],
        ];

        $crawler = $loggedClient->submit($form, $data);
        $this->assertTrue($loggedClient->getResponse()->isRedirect());

        $crawler = $loggedClient->followRedirect();
        $content = $loggedClient->getResponse()->getContent();

        $this->assertContains(
            'changed_admin@test.com',
            $content
        );
        $this->assertContains(
            'changed_name',
            $content
        );
        $this->assertContains(
            'Changed full name',
            $content
        );
        $this->assertContains(
            'Administrator, Manager',
            $content
        );
    }
}
