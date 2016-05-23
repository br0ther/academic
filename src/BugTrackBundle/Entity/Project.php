<?php

namespace BugTrackBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="BugTrackBundle\Repository\ProjectRepository")
 * @UniqueEntity(fields="label", message="Sorry, this label is already in use.")
 * @UniqueEntity(fields="code", message="Sorry, this code is already in use.")
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, unique=true)
     * 
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The label is too short. It must be at least 2 characters long",
     *     maxMessage="The name is too long"
     * )
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, unique=true)
     * 
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="The code is too short. It must be at least 2 characters long",
     *     maxMessage="The name is too long"
     * )
     * 
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     * @ORM\JoinTable(name="project_users",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $members;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     */
    protected $issues;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->issues = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Project
     */
    public function setlabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getlabel()
    {
        return $this->label;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Project
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Project
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Add member
     *
     * @param User $member
     * @return Project
     */
    public function addMember(User $member)
    {
        $member->addProject($this);
        $this->members[] = $member;

        return $this;
    }

    /**
     * Remove member
     *
     * @param User $member
     */
    public function removeMember(User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }
    
    /**
     * Add issue
     *
     * @param Issue $issue
     * @return Project
     */
    public function addIssue(Issue $issue)
    {
        $this->issues[] = $issue;

        return $this;
    }

    /**
     * Remove issue
     *
     * @param Issue $issue
     */
    public function removeIssue(Issue $issue)
    {
        $this->issues->removeElement($issue);
    }

    /**
     * Get issues
     *
     * @return ArrayCollection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Get MembersFullNames
     *
     * @return array
     */
    public function getMembersFullNames()
    {
        $membersFullNames = [];

        /** @var User $member */
        foreach ($this->getMembers() as $member) {
            $membersFullNames[] = $member->getFullName();
        }
        
        return $membersFullNames;
    }
}
