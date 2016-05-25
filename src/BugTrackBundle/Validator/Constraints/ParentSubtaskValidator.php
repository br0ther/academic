<?php

namespace BugTrackBundle\Validator\Constraints;

use BugTrackBundle\DBAL\Type\IssueType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use BugTrackBundle\Entity\Issue;

class ParentSubtaskValidator extends ConstraintValidator
{
    /**
     * @param Issue $issue
     * @param Constraint $constraint
     */
    public function validate($issue, Constraint $constraint)
    {
        if ((!empty($issue->getParentIssue()) && $issue->getType() !== IssueType::TYPE_SUBTASK) ||
            (empty($issue->getParentIssue()) && $issue->getType() === IssueType::TYPE_SUBTASK)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('parentIssue')
                ->addViolation();
        }
    }
}
