<?php

namespace BugTrackBundle\Twig;

use BugTrackBundle\Entity\Timezone;
use BugTrackBundle\Entity\User;
use DateTime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class ActivitiesExtension
 * @package BugTrackBundle\Twig
 */
class DateTimeExtension extends \Twig_Extension
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'datetime_extension';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('tz_datetime', [$this, 'getTzDatetime']),
        ];
    }

    /**
     *
     * @return User|null
     */
    public function getUserTimezone()
    {
        if ($this->tokenStorage->getToken()) {
            return $this->tokenStorage->getToken()->getUser()->getTimezone();
        }
    }
    
    /**
     * Returns datetime according to timezone
     *
     * @param DateTime $dateTime
     * @param string   $format
     *
     * @return DateTime
     */
    public function getTzDatetime(DateTime $dateTime, $format = 'Y-m-d H:i:s')
    {
        /** @var Timezone $timezone */
        $timezone = $this->getUserTimezone();
        $convertedDateTime = clone $dateTime;

        if ($timezone) {
            $tzName = $timezone->getName();

            try {
                $timezoneObject = new \DateTimeZone($tzName);
            } catch (\Exception $e) {
                throw new NotFoundHttpException('Invalid timezone provided');
            }
            $convertedDateTime->setTimezone($timezoneObject);
        }

        if ($format) {
            $convertedDateTime = $convertedDateTime->format($format);
        }

        return $convertedDateTime;
    }
}
