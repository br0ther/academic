<?php

namespace BugTrackBundle\DBAL\Type;

/**
 * User type
 */
class UserType extends AbstractEnumType
{
    const ROLE_OPERATOR = 'ROLE_OPERATOR';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var string Name of this type
     */
    protected $name = 'user_type';

    /**
     * @var array Readable choices
     *
     * @static
     */
    protected static $choices = [
        self::ROLE_OPERATOR => 'Operator',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_ADMIN => 'Administrator',
    ];
}
