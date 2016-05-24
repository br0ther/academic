<?php
namespace BugTrackBundle\DataFixtures\ORM;

use BugTrackBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadUserData
 * @package BugTrackBundle\DataFixtures\ORM
 */
class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $fosUserManager = $this->container->get('fos_user.user_manager');

        /** @var User $userAdmin */
        $userAdmin = $fosUserManager->createUser();
        $userAdmin
            ->setEmail('admin@test.com')
            ->setUsername('admin')
            ->setPlainPassword('123456')
            ->setFullName('Andy Admin')
            ->setEnabled(true)
            ->addRole('ROLE_ADMIN');

        $fosUserManager->updateUser($userAdmin);

        /** @var User $userManager */
        $userManager = $fosUserManager->createUser();
        $userManager
            ->setEmail('manager@test.com')
            ->setUsername('manager')
            ->setPlainPassword('123456')
            ->setFullName('Andy Manager')
            ->setEnabled(true)
            ->addRole('ROLE_MANAGER');

        $fosUserManager->updateUser($userManager);

        /** @var User $userOperator */
        $userOperator = $fosUserManager->createUser();
        $userOperator
            ->setEmail('operator@test.com')
            ->setUsername('operator')
            ->setPlainPassword('123456')
            ->setFullName('Andy Operator')
            ->setEnabled(true)
            ->addRole('ROLE_OPERATOR');

        $fosUserManager->updateUser($userOperator);

        $this->addReference('userAdmin', $userAdmin);
        $this->addReference('userManager', $userManager);
        $this->addReference('userOperator', $userOperator);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
