<?php

namespace BugTrackBundle\Security\Voter;

use BugTrackBundle\Entity\Project;
use BugTrackBundle\Security\Credential;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends AbstractUserVoter
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedClasses()
    {
        return [Project::class];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedAttributes()
    {
        return [
            Credential::VIEW_PROJECT,
            Credential::EDIT_PROJECT,
            Credential::CREATE_ISSUE,
        ];
    }

    /**
     * @param string        $attribute
     * @param Project       $project
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isGrantedForUser($attribute, $project, UserInterface $user)
    {
        switch ($attribute) {
            case Credential::VIEW_PROJECT:
            case Credential::EDIT_PROJECT:
            case Credential::CREATE_ISSUE:
                $isGranted = static::isAdmin($user) || static::isManager($user)
                    || (static::isOperator($user) && $project->getMembers()->contains($user));
                break;
            default:
                $isGranted = false;
        }

        return $isGranted;
    }
}
