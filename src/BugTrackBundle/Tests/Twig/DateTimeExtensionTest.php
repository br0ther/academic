<?php

namespace BugTrackBundle\Tests\Twig;

use BugTrackBundle\Entity\Timezone;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Twig\DateTimeExtension;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DateTimeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * SetUp
     */
    public function setUp()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers \BugTrackBundle\Twig\DateTimeExtension::getTzDatetime()
     */
    public function testGetTzDatetimeExtension()
    {
        $tokenStorage = $this->getMock(TokenStorage::class);
        
        $dateTimeExtension = new DateTimeExtension($tokenStorage);
        $currentDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        
        $converted = $dateTimeExtension->getTzDatetime($currentDateTime);

        $this->assertEquals($converted, $currentDateTime->format('Y-m-d H:i:s'));

        $user = $this->getMock(User::class);
        $timezone = $this->getMock(Timezone::class);
        $user->method('getTimezone')->willReturn($timezone);
        $timezone->method('getName')->willReturn('America/Los_Angeles');
        
        $tokenInterface = $this->getMock(TokenInterface::class);
        $tokenInterface->method('getUser')->willReturn($user);
        $tokenStorage->method('getToken')->willReturn($tokenInterface);

        $converted = $dateTimeExtension->getTzDatetime($currentDateTime);

        $this->assertEquals(
            $converted,
            $currentDateTime->setTimezone(new \DateTimeZone('America/Los_Angeles'))->format('Y-m-d H:i:s')
        );
    }
}
