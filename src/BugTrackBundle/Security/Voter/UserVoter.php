<?php

namespace BugTrackBundle\Security\Voter;

use BugTrackBundle\Entity\User;
use BugTrackBundle\Security\Credential;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends AbstractUserVoter
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedClasses()
    {
        return [User::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedAttributes()
    {
        return [
            Credential::EDIT_ROLES,
            Credential::CREATE_PROJECT,
            Credential::EDIT_PROFILE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGrantedForUser($attribute, $object, UserInterface $user)
    {
        switch ($attribute) {
            case Credential::EDIT_ROLES:
                $isGranted = static::isAdmin($user);
                break;
            case Credential::CREATE_PROJECT:
                $isGranted = static::isAdmin($user) || static::isManager($user);
                break;
            case Credential::EDIT_PROFILE:
                $isGranted = $this->isUserOwner($user, $object);
                break;
            default:
                $isGranted = false;
        }

        return $isGranted;
    }
}
