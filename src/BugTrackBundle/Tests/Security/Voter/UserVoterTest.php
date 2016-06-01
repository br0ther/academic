<?php

namespace BugTrackBundle\Tests\Security\Voter;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use BugTrackBundle\Security\Voter\UserVoter;

/**
 * Class UserVoterTest
 * @package BugTrackBundle\Tests\Security\Voter
 */
class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function userProvider()
    {
        return [
            [Credential::EDIT_PROFILE, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_PROFILE, UserType::ROLE_MANAGER, false, false],
            [Credential::EDIT_PROFILE, UserType::ROLE_ADMIN, false, true],

            [Credential::EDIT_PROFILE, UserType::ROLE_OPERATOR, true, true],
            [Credential::EDIT_PROFILE, UserType::ROLE_MANAGER, true, true],
            [Credential::EDIT_PROFILE, UserType::ROLE_ADMIN, true, true],
            
            [Credential::EDIT_ROLES, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_ROLES, UserType::ROLE_MANAGER, false, false],
            [Credential::EDIT_ROLES, UserType::ROLE_ADMIN, false, true],

            [Credential::EDIT_ROLES, UserType::ROLE_OPERATOR, true, false],
            [Credential::EDIT_ROLES, UserType::ROLE_MANAGER, true, false],
            [Credential::EDIT_ROLES, UserType::ROLE_ADMIN, true, true],

            [Credential::CREATE_PROJECT, UserType::ROLE_OPERATOR, false, false],
            [Credential::CREATE_PROJECT, UserType::ROLE_MANAGER, false, true],
            [Credential::CREATE_PROJECT, UserType::ROLE_ADMIN, false, true],

            [Credential::CREATE_PROJECT, UserType::ROLE_OPERATOR, true, false],
            [Credential::CREATE_PROJECT, UserType::ROLE_MANAGER, true, true],
            [Credential::CREATE_PROJECT, UserType::ROLE_ADMIN, true, true],
        ];
    }

    /**
     * @param $attribute
     * @param $role
     * @param $owner
     * @param $expected
     * 
     * @dataProvider userProvider
     */
    public function testIsGrantedForUser($attribute, $role, $owner, $expected)
    {
        $userVoter = new UserVoter();
        $userVoterReflection = new \ReflectionClass(UserVoter::class);
        $method = $userVoterReflection->getMethod('isGrantedForUser');
        $method->setAccessible(true);

        $user = $this->getMock(User::class);
        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn([$role]);
        
        if ($owner) {
            $checkedUser = $user;
        } else {
            $checkedUser = $this->getMock(User::class);
        }
        
        $this->assertEquals(
            $expected,
            $method->invokeArgs($userVoter, [$attribute, $checkedUser, $user]),
            sprintf("%s (%s) OWNER: %s", $role, $attribute, (int) $owner)
        );
    }
}
