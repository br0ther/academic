<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Issue type
 */
class IssueType extends AbstractEnumType
{
    const TYPE_BUG = 'Bug';
    const TYPE_SUBTASK = 'Subtask';
    const TYPE_TASK = 'Task';
    const TYPE_STORY = 'Story';

    /**
     * @var string Name of this type
     */
    protected $name = 'issue_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::TYPE_BUG => self::TYPE_BUG,
        self::TYPE_SUBTASK => self::TYPE_SUBTASK,
        self::TYPE_TASK => self::TYPE_TASK,
        self::TYPE_STORY => self::TYPE_STORY,
    ];
}
