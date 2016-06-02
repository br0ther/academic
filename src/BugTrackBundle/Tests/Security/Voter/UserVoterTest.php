<?php

namespace BugTrackBundle\Tests\Security\Voter;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use BugTrackBundle\Security\Voter\UserVoter;
use BugTrackBundle\Tests\PHPUnitHelperTrait;

/**
 * Class UserVoterTest
 * @package BugTrackBundle\Tests\Security\Voter
 */
class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    use PHPUnitHelperTrait;
    
    /**
     * @return array
     */
    public function userProvider()
    {
        return [
            [Credential::EDIT_PROFILE, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_PROFILE, UserType::ROLE_MANAGER, false, false],
            [Credential::EDIT_PROFILE, UserType::ROLE_ADMIN, false, true],
            [Credential::EDIT_PROFILE, 'UNDEFINED_ROLE', false, false],

            [Credential::EDIT_PROFILE, UserType::ROLE_OPERATOR, true, true],
            [Credential::EDIT_PROFILE, UserType::ROLE_MANAGER, true, true],
            [Credential::EDIT_PROFILE, UserType::ROLE_ADMIN, true, true],
            [Credential::EDIT_PROFILE, 'UNDEFINED_ROLE', true, true],
            
            [Credential::EDIT_ROLES, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_ROLES, UserType::ROLE_MANAGER, false, false],
            [Credential::EDIT_ROLES, UserType::ROLE_ADMIN, false, true],
            [Credential::EDIT_ROLES, 'UNDEFINED_ROLE', false, false],

            [Credential::EDIT_ROLES, UserType::ROLE_OPERATOR, true, false],
            [Credential::EDIT_ROLES, UserType::ROLE_MANAGER, true, false],
            [Credential::EDIT_ROLES, UserType::ROLE_ADMIN, true, true],
            [Credential::EDIT_ROLES, 'UNDEFINED_ROLE', true, false],

            [Credential::CREATE_PROJECT, UserType::ROLE_OPERATOR, false, false],
            [Credential::CREATE_PROJECT, UserType::ROLE_MANAGER, false, true],
            [Credential::CREATE_PROJECT, UserType::ROLE_ADMIN, false, true],
            [Credential::CREATE_PROJECT, 'UNDEFINED_ROLE', true, false],
            
            [Credential::CREATE_PROJECT, UserType::ROLE_OPERATOR, true, false],
            [Credential::CREATE_PROJECT, UserType::ROLE_MANAGER, true, true],
            [Credential::CREATE_PROJECT, UserType::ROLE_ADMIN, true, true],
            [Credential::CREATE_PROJECT, 'UNDEFINED_ROLE', true, false],
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
            $this->invokeMethod(
                new UserVoter(),
                'isGrantedForUser',
                [$attribute, $checkedUser, $user]
            ),
            sprintf("%s (%s) OWNER: %s", $role, $attribute, (int) $owner)
        );
    }
}
