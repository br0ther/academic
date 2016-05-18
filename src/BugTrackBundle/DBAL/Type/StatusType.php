<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Status type
 */
class StatusType extends AbstractEnumType
{
    const STATUS_OPEN = 'Open';
    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_RESOLVED = 'Resolved';
    const STATUS_CLOSED= 'Closed';

    /**
     * @var string Name of this type
     */
    protected $name = 'status_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::STATUS_OPEN => self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
        self::STATUS_RESOLVED => self::STATUS_RESOLVED,
        self::STATUS_CLOSED => self::STATUS_CLOSED,
    ];
}
