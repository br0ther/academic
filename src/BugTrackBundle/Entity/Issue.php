<?php

namespace BugTrackBundle\Entity;

use BugTrackBundle\Validator\Constraints as BugTrackConstraints;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as GedmoAnnotations;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="BugTrackBundle\Entity\Repository\IssueRepository")
 * 
 * @UniqueEntity(fields="summary", message="Sorry, this summary is already in use.")
 * @UniqueEntity(fields="code", message="Sorry, this code is already in use.")
 *
 * @ORM\EntityListeners({"BugTrackBundle\Entity\Listener\IssueEntityListener"})
 *
 * @BugTrackConstraints\ParentSubtask()
 * @BugTrackConstraints\ResolutionNonResolved()
 */
class Issue
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
     * @ORM\Column(name="summary", type="string", length=255, unique=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="issue_type", length=20)
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="priority_type", length=20)
     */
    protected $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="status_type", length=20)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="resolution", type="resolution_type", length=20, nullable=true)
     */
    protected $resolution;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reportedIssues")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assignedIssues")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assignee;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="issues")
     * @ORM\JoinTable(name="issue_collaborators",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $collaborators;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="childIssues")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parentIssue;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parentIssue")
     */
    protected $childIssues;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="issues")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var \DateTime $created
     *
     * @GedmoAnnotations\Timestampable(on="create")
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @GedmoAnnotations\Timestampable(on="update")
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="issue")
     */
    protected $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IssueActivity", mappedBy="issue")
     */
    protected $activities;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
        $this->childIssues = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->activities = new ArrayCollection();
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
     * Set summary
     *
     * @param string $summary
     * @return Issue
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
     * Set code
     *
     * @param string $code
     * @return Issue
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
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * @return Issue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get priority
     * 
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priority
     * 
     * @param string $priority
     * @return Issue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get Status
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     * 
     * @param string $status
     * @return Issue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get Resolution
     * 
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set Resolution
     * 
     * @param string $resolution
     * @return Issue
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

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

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Project
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set reporter
     *
     * @param User $reporter
     * @return Issue
     */
    public function setReporter(User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param User $assignee
     * @return Issue
     */
    public function setAssignee(User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set parentIssue
     *
     * @param Issue $parentIssue
     * @return Issue
     */
    public function setParentIssue(Issue $parentIssue = null)
    {
        $this->parentIssue = $parentIssue;

        return $this;
    }

    /**
     * Get parentIssue
     *
     * @return Issue
     */
    public function getParentIssue()
    {
        return $this->parentIssue;
    }

    /**
     * Add childIssue
     *
     * @param Issue $childIssue
     * @return Issue
     */
    public function addChildIssue(Issue $childIssue)
    {
        $this->childIssues[] = $childIssue;

        return $this;
    }

    /**
     * Remove childIssue
     *
     * @param Issue $childIssue
     */
    public function removeChildIssue(Issue $childIssue)
    {
        $this->childIssues->removeElement($childIssue);
    }

    /**
     * Get childIssues
     *
     * @return ArrayCollection
     */
    public function getChildIssues()
    {
        return $this->childIssues;
    }

    /**
     * Add collaborator
     *
     * @param User $collaborator
     * @return Issue
     */
    public function addCollaborator(User $collaborator)
    {
        $collaborator->addIssue($this);
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator
     *
     * @param User $collaborator
     */
    public function removeCollaborator(User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Get collaborators
     *
     * @return ArrayCollection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Add activity
     *
     * @param IssueActivity $activity
     * @return Issue
     */
    public function addActivity(IssueActivity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity
     *
     * @param IssueActivity $activity
     */
    public function removeActivity(IssueActivity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities
     *
     * @return ArrayCollection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     * @return Issue
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set project
     *
     * @param Project $project
     * @return Issue
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * getTitle
     * @return string
     */
    public function getTitle()
    {
        return sprintf('[%s] %s', $this->getCode(), $this->getSummary());
    }

    /**
     * Get CollaboratorsFullNames
     *
     * @return array
     */
    public function getCollaboratorsFullNames()
    {
        $collaborators = [];

        /** @var User $member */
        foreach ($this->getCollaborators() as $collaborator) {
            $collaborators[] = $collaborator->getFullName();
        }

        return $collaborators;
    }

    /**
     * Add NotExistsCollaborator
     *
     * @param User $collaborator
     * @return Issue
     */
    public function addNotExistsCollaborator(User $collaborator)
    {
        if ($collaborator && !$this->getCollaborators()->contains($collaborator)) {
            $this->addCollaborator($collaborator);
        }

        return $this;
    }
}
