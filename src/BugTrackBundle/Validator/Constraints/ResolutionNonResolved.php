<?php

namespace BugTrackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ResolutionNonResolved extends Constraint
{
    public $message = 'Resolution should provide with status Resolved';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
