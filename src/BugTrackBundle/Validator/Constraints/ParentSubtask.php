<?php

namespace BugTrackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ParentSubtask extends Constraint
{
    public $message = 'Type Subtask should provide with Parent issue';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
