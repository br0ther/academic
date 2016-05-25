<?php

namespace BugTrackBundle\Form\Subscriber;

use BugTrackBundle\Entity\Issue;
use BugTrackBundle\DBAL\Type\StatusType;
use BugTrackBundle\DBAL\Type\ResolutionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ShowFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        /** @var Issue $issue */
        $issue = $event->getData();
        $form = $event->getForm();

        if (!$issue || null === $issue->getId() ||
            $issue->getStatus() == StatusType::STATUS_RESOLVED) {
            $form->add('resolution', ChoiceType::class, [
                'choices' => array_flip(ResolutionType::getChoices()),
                'choices_as_values' => true,
                'required' => false,
            ]);
        }
    }
}
