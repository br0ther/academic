<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Resolution type
 */
class ResolutionType extends AbstractEnumType
{
    const RESOLUTION_FIXED = 'fixed';
    const RESOLUTION_WONT_FIX = 'wont_fix';
    const STATUS_DONE = 'done';

    /**
     * @var string Name of this type
     */
    protected $name = 'resolution_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::RESOLUTION_FIXED => 'Fixed',
        self::RESOLUTION_WONT_FIX => 'Won\'t fix',
        self::STATUS_DONE => 'Done',
    ];
}
