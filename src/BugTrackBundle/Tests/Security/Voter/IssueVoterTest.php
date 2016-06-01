<?php

namespace BugTrackBundle\Tests\Security\Voter;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\Project;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use BugTrackBundle\Security\Voter\IssueVoter;
use BugTrackBundle\Tests\PHPUnitHelperTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class IssueVoterTest
 * @package BugTrackBundle\Tests\Security\Voter
 */
class IssueVoterTest extends \PHPUnit_Framework_TestCase
{
    use PHPUnitHelperTrait;
    
    /**
     * @return array
     */
    public function issueProvider()
    {
        return [
            [Credential::VIEW_ISSUE, UserType::ROLE_OPERATOR, false, false],
            [Credential::VIEW_ISSUE, UserType::ROLE_MANAGER, false, true],
            [Credential::VIEW_ISSUE, UserType::ROLE_ADMIN, false, true],

            [Credential::VIEW_ISSUE, UserType::ROLE_OPERATOR, true, true],
            [Credential::VIEW_ISSUE, UserType::ROLE_MANAGER, true, true],
            [Credential::VIEW_ISSUE, UserType::ROLE_ADMIN, true, true],

            [Credential::EDIT_ISSUE, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_ISSUE, UserType::ROLE_MANAGER, false, true],
            [Credential::EDIT_ISSUE, UserType::ROLE_ADMIN, false, true],

            [Credential::EDIT_ISSUE, UserType::ROLE_OPERATOR, true, true],
            [Credential::EDIT_ISSUE, UserType::ROLE_MANAGER, true, true],
            [Credential::EDIT_ISSUE, UserType::ROLE_ADMIN, true, true],

            [Credential::CREATE_COMMENT, UserType::ROLE_OPERATOR, false, false],
            [Credential::CREATE_COMMENT, UserType::ROLE_MANAGER, false, true],
            [Credential::CREATE_COMMENT, UserType::ROLE_ADMIN, false, true],

            [Credential::CREATE_COMMENT, UserType::ROLE_OPERATOR, true, true],
            [Credential::CREATE_COMMENT, UserType::ROLE_MANAGER, true, true],
            [Credential::CREATE_COMMENT, UserType::ROLE_ADMIN, true, true],
        ];
    }

    /**
     * @param $attribute
     * @param $role
     * @param $member
     * @param $expected
     * 
     * @dataProvider issueProvider
     */
    public function testIsGrantedForUser($attribute, $role, $member, $expected)
    {
        $user = $this->getMock(User::class);
        $user->expects($this->any())
            ->method('getRoles')
            ->willReturn([$role]);

        $issue = $this->getMock(Issue::class);

        $project = $this->getMock(Project::class);
        $members = new ArrayCollection();
        
        if ($member) {
            $members->add($user);
        }

        $project
            ->method('getMembers')
            ->willReturn($members);

        $issue
            ->method('getProject')
            ->willReturn($project);
        
        $this->assertEquals(
            $expected,
            $this->invokeMethod(
                new IssueVoter(),
                'isGrantedForUser',
                [$attribute, $issue, $user]
            ),
            sprintf("%s (%s) OWNER: %s", $role, $attribute, (int) $member)
        );
    }
}
