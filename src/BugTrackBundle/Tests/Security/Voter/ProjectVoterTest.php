<?php

namespace BugTrackBundle\Tests\Security\Voter;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\Project;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use BugTrackBundle\Security\Voter\ProjectVoter;
use BugTrackBundle\Tests\PHPUnitHelperTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProjectVoterTest
 * @package BugTrackBundle\Tests\Security\Voter
 */
class ProjectVoterTest extends \PHPUnit_Framework_TestCase
{
    use PHPUnitHelperTrait;
    
    /**
     * @return array
     */
    public function projectProvider()
    {
        return [
            [Credential::VIEW_PROJECT, UserType::ROLE_OPERATOR, false, false],
            [Credential::VIEW_PROJECT, UserType::ROLE_MANAGER, false, true],
            [Credential::VIEW_PROJECT, UserType::ROLE_ADMIN, false, true],
            [Credential::VIEW_PROJECT, 'UNDEFINED_ROLE', false, false],
            
            [Credential::VIEW_PROJECT, UserType::ROLE_OPERATOR, true, true],
            [Credential::VIEW_PROJECT, UserType::ROLE_MANAGER, true, true],
            [Credential::VIEW_PROJECT, UserType::ROLE_ADMIN, true, true],
            [Credential::VIEW_PROJECT, 'UNDEFINED_ROLE', false, false],
            
            [Credential::EDIT_PROJECT, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_PROJECT, UserType::ROLE_MANAGER, false, true],
            [Credential::EDIT_PROJECT, UserType::ROLE_ADMIN, false, true],
            [Credential::VIEW_PROJECT, 'UNDEFINED_ROLE', false, false],
            
            [Credential::EDIT_PROJECT, UserType::ROLE_OPERATOR, true, true],
            [Credential::EDIT_PROJECT, UserType::ROLE_MANAGER, true, true],
            [Credential::EDIT_PROJECT, UserType::ROLE_ADMIN, true, true],
            [Credential::VIEW_PROJECT, 'UNDEFINED_ROLE', false, false],
            
            [Credential::CREATE_ISSUE, UserType::ROLE_OPERATOR, false, false],
            [Credential::CREATE_ISSUE, UserType::ROLE_MANAGER, false, true],
            [Credential::CREATE_ISSUE, UserType::ROLE_ADMIN, false, true],
            [Credential::VIEW_PROJECT, 'UNDEFINED_ROLE', false, false],
            
            [Credential::CREATE_ISSUE, UserType::ROLE_OPERATOR, true, true],
            [Credential::CREATE_ISSUE, UserType::ROLE_MANAGER, true, true],
            [Credential::CREATE_ISSUE, UserType::ROLE_ADMIN, true, true],
            [Credential::CREATE_ISSUE, 'UNDEFINED_ROLE', true, false],
        ];
    }

    /**
     * @param $attribute
     * @param $role
     * @param $member
     * @param $expected
     * 
     * @dataProvider projectProvider
     */
    public function testIsGrantedForUser($attribute, $role, $member, $expected)
    {
        $user = $this->getMock(User::class);
        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn([$role]);

        $project = $this->getMock(Project::class);
        $members = new ArrayCollection();
        
        if ($member) {
            $members->add($user);
        }

        $project
            ->method('getMembers')
            ->willReturn($members);
        
        $this->assertEquals(
            $expected,
            $this->invokeMethod(
                new ProjectVoter(),
                'isGrantedForUser',
                [$attribute, $project, $user]
            ),
            sprintf("%s (%s) OWNER: %s", $role, $attribute, (int) $member)
        );
    }
}
