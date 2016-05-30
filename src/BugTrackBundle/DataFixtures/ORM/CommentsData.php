<?php
namespace BugTrackBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BugTrackBundle\Entity\Comment;

/**
 * Class CommentsData
 * @package BugTrackBundle\DataFixtures\ORM
 */
class CommentsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $comment1 = new Comment();
        $comment1->setIssue($this->getReference('issueSubTask'));
        $comment1->setAuthor($this->getReference('userOperator'));
        $comment1->setBody(
            <<<EOT
For the major quirky types/values is_null(var) obviously always returns the opposite of isset(var), 
and the notice clearly points out the faulty line with the is_null() statement. 
EOT
        );

        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setIssue($this->getReference('issueSubTask'));
        $comment2->setAuthor($this->getReference('userOperator2'));
        $comment2->setBody(
            <<<EOT
You might want to examine the return value of those functions in detail, 
but since both are specified to return boolean types there should be no doubt.
EOT
        );

        $manager->persist($comment2);

        $manager->flush();

        $this->addReference('comment1', $comment1);
        $this->addReference('comment2', $comment2);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }
}
