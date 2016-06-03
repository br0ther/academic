<?php

namespace BugTrackBundle\Form\Type;

use BugTrackBundle\DBAL\Type\UserType;
use BugTrackBundle\Entity\Timezone;
use BugTrackBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserFormType
 * @package BugTrackBundle\Form\Type
 */
class UserFormType extends AbstractType
{
    /**
     * Can edit roles ?
     * @var boolean
     */
    private $canEditRoles = false;
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('fullName', TextType::class, [
                'required' => true,
            ])
            ->add('timezone', EntityType::class, [
                'choice_label' => 'getName',
                'class' => Timezone::class,
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => array_flip(UserType::getChoices()),
                'choices_as_values' => true,
                'multiple' => true,
                'disabled' => !$this->isCanEditRoles()
            ]);
    }

    /**
     * @return boolean
     */
    public function isCanEditRoles()
    {
        return $this->canEditRoles;
    }

    /**
     * @param boolean $canEditRoles
     */
    public function setCanEditRoles($canEditRoles)
    {
        $this->canEditRoles = $canEditRoles;
    }
}
