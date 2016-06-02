<?php

namespace BugTrackBundle\Twig;

use BugTrackBundle\DBAL\Type\ActivityType;
use BugTrackBundle\Entity\ActivityListableInterface;
use BugTrackBundle\Entity\IssueActivity;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ActivitiesExtension
 * @package BugTrackBundle\Twig
 */
class ActivitiesExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param EntityManager       $em
     * @param Router              $router
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $em, Router $router, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'activities_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_activities', [$this, 'getActivities']),
            new \Twig_SimpleFunction('get_activity_title', [$this, 'getActivityTitle']),
        ];
    }

    /**
     * getActivities
     *
     * @param ActivityListableInterface $object
     *
     * @return IssueActivity[]
     */
    public function getActivities(ActivityListableInterface $object)
    {
        return $this->em->getRepository('BugTrackBundle:IssueActivity')->getActivitiesForEntity($object);
    }

    /**
     * @param IssueActivity $activity
     * @return string
     */
    public function getActivityTitle(IssueActivity $activity)
    {
        switch ($activity->getType()) {
            case ActivityType::ACTIVITY_ISSUE_ADDED:
                $title = $this->translator->trans(
                    'activity.issue_added',
                    [],
                    'BugTrackBundle'
                );
                break;
            case ActivityType::ACTIVITY_ISSUE_CHANGED:
                $title = $this->translator->trans(
                    'activity.issue_changed',
                    [
                        '%field%' => $activity->getChangedField(),
                        '%old%' => $activity->getOldValue(),
                        '%new%' => $activity->getNewValue(),
                    ],
                    'BugTrackBundle'
                );
                break;
            case ActivityType::ACTIVITY_COMMENT_ADDED:
                $title = $this->translator->trans(
                    'activity.comment_added',
                    [
                        '%url%' => $this->getCommentUrl($activity),
                    ],
                    'BugTrackBundle'
                );
                break;
            case ActivityType::ACTIVITY_COMMENT_CHANGED:
                $title = $this->translator->trans(
                    'activity.comment_changed',
                    [
                        '%field%' => $activity->getChangedField(),
                        '%old%' => $activity->getOldValue(),
                        '%new%' => $activity->getNewValue(),
                        '%url%' => $this->getCommentUrl($activity),
                    ],
                    'BugTrackBundle'
                );
                break;
            default:
                throw new NotFoundHttpException('Wrong Activity type provided');
        }
        return $title;
    }

    /**
     * @param IssueActivity $activity
     *
     * @return string
     */
    protected function getCommentUrl(IssueActivity $activity)
    {
        $issueUrl = $this->router->generate('issue_view', ['id' => $activity->getIssue()->getId()]);
        $commentAnchor = '#comment-' . $activity->getComment()->getId();

        return $issueUrl . $commentAnchor;
    }
}
