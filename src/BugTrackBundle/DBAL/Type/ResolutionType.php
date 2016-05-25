<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * Resolution type
 */
class ResolutionType extends AbstractEnumType
{
    const RESOLUTION_FIXED = 'Fixed';
    const RESOLUTION_WONT_FIX = 'Wont fix';
    const STATUS_DONE = 'Done';

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
        self::RESOLUTION_FIXED => self::RESOLUTION_FIXED,
        self::RESOLUTION_WONT_FIX => self::RESOLUTION_WONT_FIX,
        self::STATUS_DONE => self::STATUS_DONE,
    ];
}
