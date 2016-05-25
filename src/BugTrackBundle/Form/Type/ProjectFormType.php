<?php

namespace BugTrackBundle\Form\Type;

use BugTrackBundle\Entity\Project;
use BugTrackBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProjectFormType
 * @package BugTrackBundle\Form\Type
 */
class ProjectFormType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class)
            ->add('code', TextType::class, [
                'attr' => [
                    'maxlength' => 10,
                ]
            ])
            ->add('summary', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'class' => 'resize-vertical',
                ]
            ])
            ->add('members', EntityType::class, [
                'choice_label' => 'fullName',
                'required' => false,
                'class' => User::class,
                'multiple'  => true,
        ]);
    }
}
