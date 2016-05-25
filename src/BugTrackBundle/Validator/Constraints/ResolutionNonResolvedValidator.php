<?php

namespace BugTrackBundle\Validator\Constraints;

use BugTrackBundle\DBAL\Type\IssueType;
use BugTrackBundle\DBAL\Type\StatusType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use BugTrackBundle\Entity\Issue;

class ResolutionNonResolvedValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if ((!empty($issue->getResolution()) && $issue->getStatus() !== StatusType::STATUS_RESOLVED) ||
            (empty($issue->getResolution()) && $issue->getStatus() === StatusType::STATUS_RESOLVED)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('resolution')
                ->addViolation();
        }
    }
}
