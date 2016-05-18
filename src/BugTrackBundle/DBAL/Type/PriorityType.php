<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Priority type
 */
class PriorityType extends AbstractEnumType
{
    const PRIORITY_MAJOR = 'Major';
    const PRIORITY_BLOCKER = 'Blocker';
    const PRIORITY_CRITICAL = 'Critical';
    const PRIORITY_MINOR = 'Minor';
    const PRIORITY_TRIVIAL = 'Trivial';

    /**
     * @var string Name of this type
     */
    protected $name = 'priority_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::PRIORITY_MAJOR => self::PRIORITY_MAJOR,
        self::PRIORITY_BLOCKER => self::PRIORITY_BLOCKER,
        self::PRIORITY_CRITICAL => self::PRIORITY_CRITICAL,
        self::PRIORITY_MINOR => self::PRIORITY_MINOR,
        self::PRIORITY_TRIVIAL => self::PRIORITY_TRIVIAL,
    ];
}
