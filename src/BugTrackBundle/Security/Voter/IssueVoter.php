<?php

namespace BugTrackBundle\Security\Voter;

use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Security\Credential;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueVoter extends AbstractUserVoter
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedClasses()
    {
        return [Issue::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedAttributes()
    {
        return [
            Credential::VIEW_ISSUE,
            Credential::EDIT_ISSUE,
            Credential::CREATE_COMMENT,
        ];
    }

    /**
     * @param string        $attribute
     * @param Issue         $issue
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isGrantedForUser($attribute, $issue, UserInterface $user)
    {
        switch ($attribute) {
            case Credential::VIEW_ISSUE:
            case Credential::EDIT_ISSUE:
            case Credential::CREATE_COMMENT:
                $isGranted = static::isAdmin($user) || static::isManager($user)
                    || (static::isOperator($user) && $issue->getProject()->getMembers()->contains($user));
                break;
            default:
                $isGranted = false;
        }

        return $isGranted;
    }
}
