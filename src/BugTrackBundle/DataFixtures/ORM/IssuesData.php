<?php

namespace BugTrackBundle\DataFixtures\ORM;

use BugTrackBundle\DBAL\Type\IssueType;
use BugTrackBundle\DBAL\Type\PriorityType;
use BugTrackBundle\DBAL\Type\ResolutionType;
use BugTrackBundle\DBAL\Type\StatusType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BugTrackBundle\Entity\Issue;

/**
 * Class IssuesData
 * @package TrackerBundle\DataFixtures\ORM
 */
class IssuesData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $issue1 = new Issue();
        $issue1->setSummary('Issue templating');
        $issue1->setCode('ISS-01');
        $issue1->setDescription(
            <<<EOT
When opening new issues it would help guiding the user to be as detailed as possible if an administrator 
could create a predefined template per tracker (per project?). Such a template need only be displayed as stand in
 text in the description field so that the user can then replace template parts with the actual input.
EOT
        );
        $issue1->setType(IssueType::TYPE_STORY);
        $issue1->setPriority(PriorityType::PRIORITY_MAJOR);
        $issue1->setStatus(StatusType::STATUS_OPEN);
        $issue1->setProject($this->getReference('projectA'));
        $issue1->setReporter($this->getReference('userOperator'));

        $manager->persist($issue1);

        $issue2 = new Issue();
        $issue2->setSummary('Issue change templating');
        $issue2->setCode('ISS-02');
        $issue2->setDescription(
            <<<EOT
Think about how to change templating engine
EOT
        );
        $issue2->setType(IssueType::TYPE_SUBTASK);
        $issue2->setPriority(PriorityType::PRIORITY_BLOCKER);
        $issue2->setStatus(StatusType::STATUS_OPEN);
        $issue2->setProject($this->getReference('projectA'));
        $issue2->setReporter($this->getReference('userOperator'));
        $issue2->setParentIssue($issue1);

        $manager->persist($issue2);

        $issue3 = new Issue();
        $issue3->setSummary('Wrong parsing links in Description page');
        $issue3->setCode('ISS-03');
        $issue3->setDescription(
            <<<EOT
Links appears to be broken. See attached screenshot.
EOT
        );
        $issue3->setType(IssueType::TYPE_BUG);
        $issue3->setPriority(PriorityType::PRIORITY_MINOR);
        $issue3->setStatus(StatusType::STATUS_RESOLVED);
        $issue3->setProject($this->getReference('projectA'));
        $issue3->setReporter($this->getReference('userOperator2'));
        $issue3->setAssignee($this->getReference('userOperator2'));
        $issue3->setResolution(ResolutionType::RESOLUTION_FIXED);
        $issue3->setParentIssue($issue1);

        $manager->persist($issue3);

        $manager->flush();

        $this->addReference('issueStory', $issue1);
        $this->addReference('issueSubTask', $issue2);
        $this->addReference('issueResolved', $issue3);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
}
