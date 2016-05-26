<?php
namespace BugTrackBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use BugTrackBundle\Entity\Project;

/**
 * Class ProjectsData
 * @package BugTrackBundle\DataFixtures\ORM
 */
class ProjectsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $projectA = new Project();
        $projectA->setLabel('Project A');
        $projectA->setCode('ABC');
        $projectA->setSummary(
            <<<EOT
On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized
 by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble
 that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, 
 which is the same as saying through shrinking from toil and pain. These cases are perfectly simple
 and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents 
 our being able to do what we like best, every pleasure is to be welcomed and every pain avoided.
EOT
        );
        $projectA->addMember($this->getReference('userManager'));
        $projectA->addMember($this->getReference('userOperator'));
        $manager->persist($projectA);

        $projectB = new Project();
        $projectB->setLabel('Project B');
        $projectB->setCode('ABCD');
        $projectB->setSummary(
            <<<EOT
But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently
 occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these
  matters to this principle of selection: he rejects pleasures to secure other greater pleasures,
  or else he endures pains to avoid worse pains.
EOT
        );
        $projectB->addMember($this->getReference('userAdmin'));
        $manager->persist($projectB);
        $manager->flush();

        $this->addReference('projectA', $projectA);
        $this->addReference('projectB', $projectB);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
}
