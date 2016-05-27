<?php

namespace BugTrackBundle\Form\Type;

use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BugTrackBundle\Entity\Comment;

class CommentFormType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('issue', EntityType::class, [
                'choice_label' => 'getTitle',
                'disabled' => true,
                'class' => Issue::class,
            ])
            ->add('author', EntityType::class, [
                'choice_label' => 'fullName',
                'disabled' => true,
                'class' => User::class,
            ])
            ->add('body', TextareaType::class, [
                'label' => 'Content',
                'attr' => [
                    'rows' => 3,
                    'class' => 'resize-vertical',
                ]
            ]);
    }
}
