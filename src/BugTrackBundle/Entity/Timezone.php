<?php

namespace BugTrackBundle\Entity;

use Gedmo\Mapping\Annotation as GedmoAnnotations;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Timezone
 *
 * @package BugTrackBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="timezone")
 */
class Timezone
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="integer", nullable=false)
     */
    protected $offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive = false;

    /**
     * @var \DateTime $createdAt
     *
     * @GedmoAnnotations\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @GedmoAnnotations\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return Timezone
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Country
     *
     * @param string $country
     *
     * @return Timezone
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get City
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set City
     *
     * @param string $city
     *
     * @return Timezone
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get Offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set Offset
     *
     * @param integer $offset
     *
     * @return Timezone
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Get formatted offset
     *
     * @return string
     */
    public function getFormattedOffset()
    {
        $sign = $this->offset < 0 ? '-' : '+';
        $offset = abs($this->offset);

        return sprintf('%s%02d:%02d', $sign, floor($offset / 3600), $offset % 3600);
    }

    /**
     * Get IsActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set IsActive
     *
     * @param boolean $isActive
     *
     * @return Timezone
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Is active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Get CreatedAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set CreatedAt
     *
     * @param \DateTime $createdAt
     *
     * @return Timezone
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get UpdatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set UpdatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Timezone
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns string representation of the entity.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
