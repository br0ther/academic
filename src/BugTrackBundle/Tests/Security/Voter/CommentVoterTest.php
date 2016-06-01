<?php

namespace BugTrackBundle\Tests\Security\Voter;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use BugTrackBundle\Security\Voter\CommentVoter;

/**
 * Class CommentVoterTest
 * @package BugTrackBundle\Tests\Security\Voter
 */
class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function commentProvider()
    {
        return [
            [Credential::EDIT_COMMENT, UserType::ROLE_OPERATOR, false, false],
            [Credential::EDIT_COMMENT, UserType::ROLE_MANAGER, false, false],
            [Credential::EDIT_COMMENT, UserType::ROLE_ADMIN, false, true],

            [Credential::EDIT_COMMENT, UserType::ROLE_OPERATOR, true, true],
            [Credential::EDIT_COMMENT, UserType::ROLE_MANAGER, true, true],
            [Credential::EDIT_COMMENT, UserType::ROLE_ADMIN, true, true],

            [Credential::DELETE_COMMENT, UserType::ROLE_OPERATOR, false, false],
            [Credential::DELETE_COMMENT, UserType::ROLE_MANAGER, false, false],
            [Credential::DELETE_COMMENT, UserType::ROLE_ADMIN, false, true],

            [Credential::DELETE_COMMENT, UserType::ROLE_OPERATOR, true, true],
            [Credential::DELETE_COMMENT, UserType::ROLE_MANAGER, true, true],
            [Credential::DELETE_COMMENT, UserType::ROLE_ADMIN, true, true],
        ];
    }

    /**
     * @param $attribute
     * @param $role
     * @param $owner
     * @param $expected
     * 
     * @dataProvider commentProvider
     */
    public function testIsGrantedForUser($attribute, $role, $owner, $expected)
    {
        $commentVoter = new CommentVoter();
        $commentVoterReflection = new \ReflectionClass(CommentVoter::class);
        $method = $commentVoterReflection->getMethod('isGrantedForUser');
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
        
        $comment = $this->getMock(Comment::class);
        $comment->expects($this->any())
            ->method('getAuthor')
            ->willReturn($checkedUser);
        
        $this->assertEquals(
            $expected,
            $method->invokeArgs($commentVoter, [$attribute, $comment, $user]),
            sprintf("%s (%s) OWNER: %s", $role, $attribute, (int) $owner)
        );
    }
}
