<?php

namespace BugTrackBundle\Form\Type;

use BugTrackBundle\DBAL\Type\IssueType;
use BugTrackBundle\DBAL\Type\PriorityType;
use BugTrackBundle\DBAL\Type\StatusType;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Entity\Project;
use BugTrackBundle\Entity\User;
use BugTrackBundle\Form\Subscriber\ShowFieldSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IssueFormType
 * @package BugTrackBundle\Form\Type
 */
class IssueFormType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * IssueFormType constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Issue::class,
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('project', EntityType::class, [
                'choice_label' => 'getTitle',
                'required' => true,
                'class' => Project::class,
                ])
            ->add('code', TextType::class, [
                'attr' => [
                    'maxlength' => 10,
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => array_flip(IssueType::getChoices()),
                'choices_as_values' => true,
                ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(StatusType::getChoices()),
                'choices_as_values' => true,
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => array_flip(PriorityType::getChoices()),
                'choices_as_values' => true,
            ])
            ->add('summary', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'class' => 'resize-vertical',
                ]
            ])
            ->add('parentIssue', EntityType::class, [
                'choice_label' => 'getTitle',
                'required' => false,
                'class' => Issue::class,
                'choices' => $this->getIssuesStoryType()
            ])
            ->add('reporter', EntityType::class, [
                'choice_label' => 'fullName',
                'required' => false,
                'class' => User::class,
            ])
            ->add('assignee', EntityType::class, [
                'choice_label' => 'fullName',
                'required' => false,
                'class' => User::class,
        ]);

        $builder->addEventSubscriber(new ShowFieldSubscriber());
    }

    /**
     * @return array|Issue[]
     */
    public function getIssuesStoryType()
    {
        //ToDo: make same with projects: Operator can't access projects and issues if he is not a member of this project

        return $this->entityManager->getRepository('BugTrackBundle:Issue')->findBy(
            ['type' => IssueType::TYPE_TASK],
            ['id' => 'DESC']
        );
    }
}
