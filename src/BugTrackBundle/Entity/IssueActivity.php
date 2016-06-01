<?php

namespace BugTrackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as GedmoAnnotations;

/**
 * Project
 *
 * @ORM\Table(name="issue_activity")
 * @ORM\Entity(repositoryClass="BugTrackBundle\Entity\Repository\IssueActivityRepository")
 */
class IssueActivity
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
     * @ORM\Column(name="type", type="activity_type", length=50)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="changed_field", type="string", length=255, nullable=true)
     */
    protected $changedField;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="string", length=255, nullable=true)
     */
    protected $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="string", length=255, nullable=true)
     */
    protected $newValue;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="activities")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $issue;

    /**
     * @var Comment
     *
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="activities")
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $comment;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var \DateTime $created
     *
     * @GedmoAnnotations\Timestampable(on="create")
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;
    
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * 
     * @return IssueActivity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
    
    /**
     * Set issue
     *
     * @param Issue $issue
     * 
     * @return IssueActivity
     */
    public function setIssue(Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set comment
     *
     * @param Comment $comment
     * 
     * @return IssueActivity
     */
    public function setComment(Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Issue
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set user
     *
     * @param User $user
     * 
     * @return IssueActivity
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get ChangedField
     * 
     * @return string
     */
    public function getChangedField()
    {
        return $this->changedField;
    }

    /**
     * Set ChangedField
     * 
     * @param string $changedField
     * 
     * @return IssueActivity
     */
    public function setChangedField($changedField)
    {
        $this->changedField = $changedField;

        return $this;
    }

    /**
     * Get OldValue
     * 
     * @return string
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set OldValue
     * 
     * @param string $oldValue
     * 
     * @return IssueActivity
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get NewValue
     * 
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Set NewValue
     * 
     * @param string $newValue
     * 
     * @return IssueActivity
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Project
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
