<?php

namespace BugTrackBundle\Security\Voter;


use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class AbstractUserVoter extends Voter
{
    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     *
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user)
     *
     * @param string               $attribute
     * @param object               $object
     * @param UserInterface        $user
     *
     * @return bool
     */
    abstract protected function isGrantedForUser($attribute, $object, UserInterface $user);

    /**
     * Return an array of supported classes.
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    abstract protected function getSupportedClasses();

    /**
     * Return an array of supported attributes.
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    abstract protected function getSupportedAttributes();

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, $this->getSupportedAttributes())) {
            return false;
        }

        foreach ($this->getSupportedClasses() as $supportedClass) {
            if (is_a($subject, $supportedClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $securityUser = $token->getUser();

        if (!$securityUser instanceof UserInterface) {
            return self::ACCESS_ABSTAIN;
        }

        return parent::vote($token, $object, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        return $this->isGrantedForUser($attribute, $subject, $token->getUser());
    }
    /**
     * Checks that given user owns given object
     *
     * @param UserInterface $user
     * @param object        $object
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    protected function isUserOwner(UserInterface $user, $object)
    {

        switch (true) {
            case $object instanceof User:
                $ownerOfObject = $object;
                break;
            case $object instanceof Comment:
                $ownerOfObject = $object->getAuthor();
                break;
            default:
                $msg = 'Unsupported object for retrieving owning user. Given type: ' . gettype($object);
                throw new \InvalidArgumentException($msg);
        }

        return $user && $user->getId() === $ownerOfObject->getId();
    }
    
    /**
     * Checks that given user is Admin
     *
     * @param mixed $user
     *
     * @return bool
     */
    protected static function isAdmin($user)
    {
        return in_array(UserType::ROLE_ADMIN, $user->getRoles());
    }

    /**
     * Checks that given user is Manager
     *
     * @param mixed $user
     *
     * @return bool
     */
    protected static function isManager($user)
    {
        return in_array(UserType::ROLE_MANAGER, $user->getRoles());
    }

    /**
     * Checks that given user is Operator
     *
     * @param mixed $user
     *
     * @return bool
     */
    protected static function isOperator($user)
    {
        return in_array(UserType::ROLE_OPERATOR, $user->getRoles());
    }
}
