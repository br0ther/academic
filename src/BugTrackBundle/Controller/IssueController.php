<?php

namespace BugTrackBundle\Controller;

use BugTrackBundle\Entity\Project;
use BugTrackBundle\Event\BugTrackEvents;
use BugTrackBundle\Event\IssueEvent;
use BugTrackBundle\Form\Type\IssueFormType;
use BugTrackBundle\Security\Credential;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BugTrackBundle\Entity\Issue;

/**
 * Class IssueController
 * @package BugTrackBundle\Controller
 */
class IssueController extends Controller
{
    /**
     * Issue view
     * 
     * @Route("/issue/view/{id}", name="issue_view")
     * @ParamConverter("issue", class="BugTrackBundle:Issue")
     * @Method({"GET"})
     * @Template("issue/view.html.twig")
     *
     * @param Issue $issue
     *
     * @return Response
     */
    public function viewAction(Issue $issue)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::VIEW_ISSUE,
            $issue,
            $translator->trans('credential.view_issue', [], 'BugTrackBundle')
        );

        return [
            'issue' => $issue,
        ];
    }

    /**
     * Issue create
     * 
     * @Route("/issue/create/project/{id}", name="issue_create")
     * @ParamConverter("project", class="BugTrackBundle:Project")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Project $project
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Project $project, Request $request)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::CREATE_ISSUE,
            $project,
            $translator->trans('credential.create_issue', [], 'BugTrackBundle')
        );

        $entityManager = $this->getDoctrine()->getManager();

        $issue = new Issue();
        $issue->setProject($project);
        $issue->setReporter($this->getUser());

        $pageLabel = $translator->trans('issue.create', [], 'BugTrackBundle');

        $form = $this->createForm(IssueFormType::class, $issue, [
            'label' => $pageLabel,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($issue);

            $this->get('event_dispatcher')->dispatch(
                BugTrackEvents::ISSUE_COLLABORATORS_CHECK,
                new IssueEvent($issue)
            );

            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
        ];
    }

    /**
     * Issue edit
     *
     * @Route("/issue/edit/{id}", name="issue_edit")
     * @ParamConverter("issue", class="BugTrackBundle:Issue")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Issue $issue
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Issue $issue, Request $request)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::EDIT_ISSUE,
            $issue,
            $translator->trans('credential.edit_issue', [], 'BugTrackBundle')
        );

        $pageLabel = $translator->trans('issue.edit', [], 'BugTrackBundle');
        $form = $this->createForm(IssueFormType::class, $issue, ['label' => $pageLabel]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $this->get('event_dispatcher')->dispatch(
                BugTrackEvents::ISSUE_COLLABORATORS_CHECK,
                new IssueEvent($issue)
            );

            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
            'issue' => $issue
        ];
    }
}
