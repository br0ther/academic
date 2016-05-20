<?php

namespace BugTrackBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class UserControllerTest
 * @package BugTrackBundle\Tests\Controller
 */
class UserControllerTest extends WebTestCase
{

    /**
     * Test for user login
     */
    public function testLoginPage()
    {
        $this->loadFixtures([
            '\BugTrackBundle\DataFixtures\ORM\LoadUserData',
        ]);

        $client = $this->makeClient();
        $client->restart();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
        $this->assertEquals(1, $crawler->selectLink('Register')->count());
        $this->assertEquals(1, $crawler->selectButton('Log in')->count());

        $form = $crawler->filter('form[action="/login_check"]')->form();
        $data = [
            '_username' => 'fake',
            '_password' => 'pass',
        ];
        $crawler = $client->submit($form, $data);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        
        $this->assertContains(
            'Invalid credentials',
            $client->getResponse()->getContent()
        );
        
        $data = [
            '_username' => 'manager',
            '_password' => '123456',
        ];
        $crawler = $client->submit($form, $data);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $this->assertContains(
            'Home',
            $client->getResponse()->getContent()
        );
    }
}
