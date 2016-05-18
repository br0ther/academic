<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Issue type
 */
class ActivityType extends AbstractEnumType
{
    const ACTIVITY_ISSUE_ADDED = 'issue_added';
    const ACTIVITY_STATUS_CHANGED = 'issue_status_changed';
    const ACTIVITY_COMMENT_ADDED = 'comment_added';

    /**
     * @var string Name of this type
     */
    protected $name = 'activity_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::ACTIVITY_ISSUE_ADDED => self::ACTIVITY_ISSUE_ADDED,
        self::ACTIVITY_STATUS_CHANGED => self::ACTIVITY_STATUS_CHANGED,
        self::ACTIVITY_COMMENT_ADDED => self::ACTIVITY_COMMENT_ADDED,
    ];
}
