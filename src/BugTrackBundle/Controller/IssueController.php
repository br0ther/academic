<?php

namespace BugTrackBundle\Controller;

use BugTrackBundle\Event\BugTrackEvents;
use BugTrackBundle\Event\IssueEvent;
use BugTrackBundle\Form\Type\IssueFormType;
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
     * @return Response
     */
    public function viewAction(Issue $issue)
    {
        //ToDo: add permissions & voter later

        return [
            'issue' => $issue,
        ];
    }

    /**
     * Issue create
     * 
     * @Route("/issue/create", name="issue_create")
     * @Method({"GET", "POST"})
     * @Template("issue/edit.html.twig")
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        //ToDo: add permissions & voter later
        $entityManager = $this->getDoctrine()->getManager();

        $issue = new Issue();
        $issue->setReporter($this->getUser());

        $pageLabel = $this->get('translator')->trans('issue.create', [], 'BugTrackBundle');

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
     * @return Response
     */
    public function editAction(Issue $issue, Request $request)
    {
        //ToDo: add permissions & voter later

        $pageLabel = $this->get('translator')->trans('issue.edit', [], 'BugTrackBundle');
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
