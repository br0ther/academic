<?php

namespace BugTrackBundle\Security\Voter;

use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Security\Credential;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends AbstractUserVoter
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedClasses()
    {
        return [Comment::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedAttributes()
    {
        return [
            Credential::EDIT_COMMENT,
            Credential::DELETE_COMMENT
        ];
    }

    /**
     * @param string        $attribute
     * @param Comment       $comment
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isGrantedForUser($attribute, $comment, UserInterface $user)
    {
        switch ($attribute) {
            case Credential::EDIT_COMMENT:
            case Credential::DELETE_COMMENT:
                $isGranted = static::isAdmin($user) || $this->isUserOwner($user, $comment);
                break;
            default:
                $isGranted = false;
        }

        return $isGranted;
    }
}
