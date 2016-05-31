<?php

namespace BugTrackBundle\Controller;

use BugTrackBundle\Entity\Comment;
use BugTrackBundle\Entity\Issue;
use BugTrackBundle\Event\BugTrackEvents;
use BugTrackBundle\Event\CommentEvent;
use BugTrackBundle\Form\Type\CommentFormType;
use BugTrackBundle\Security\Credential;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * @Route("/issue/{id}/comment/create/", name="comment_create")
     * @ParamConverter("issue", class="BugTrackBundle:Issue")
     * @Method({"GET", "POST"})
     * @Template("comment/edit.html.twig")
     *
     * @param Issue $issue
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Issue $issue, Request $request)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::CREATE_COMMENT,
            $issue,
            $translator->trans('credential.create_issue', [], 'BugTrackBundle')
        );

        $comment = new Comment();
        $comment->setIssue($issue);

        //ToDo: add permissions & voter later
        $comment->setAuthor($this->getUser());
        $pageLabel = $translator->trans('comment.create', [], 'BugTrackBundle');

        $form = $this->createForm(CommentFormType::class, $comment, ['label' => $pageLabel]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);

            $this->get('event_dispatcher')->dispatch(
                BugTrackEvents::COMMENT_COLLABORATORS_CHECK,
                new CommentEvent($comment)
            );

            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
            'issue' => $issue,
        ];
    }

    /**
     * @Route("/comment/edit/{id}", name="comment_edit")
     * @ParamConverter("comment", class="BugTrackBundle:Comment")
     * @Method({"GET", "POST"})
     * @Template("comment/edit.html.twig")
     *
     * @param Comment $comment
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Comment $comment, Request $request)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::EDIT_COMMENT,
            $comment,
            $translator->trans('credential.create_issue', [], 'BugTrackBundle')
        );

        $pageLabel = $translator->trans('comment.edit', [], 'BugTrackBundle');

        $form = $this->createForm(CommentFormType::class, $comment, ['label' => $pageLabel]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $this->get('event_dispatcher')->dispatch(
                BugTrackEvents::COMMENT_COLLABORATORS_CHECK,
                new CommentEvent($comment)
            );

            $entityManager->flush();

            return $this->redirectToRoute('issue_view', ['id' => $comment->getIssue()->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
            'issue' => $comment->getIssue(),
        ];
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @ParamConverter("comment", class="BugTrackBundle:Comment")
     * @Method({"GET", "POST"})
     *
     * @param Comment $comment
     * @return Response
     */
    public function deleteAction(Comment $comment)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(
            Credential::DELETE_COMMENT,
            $comment,
            $translator->trans('credential.create_issue', [], 'BugTrackBundle')
        );
        
        $issue = $comment->getIssue();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('issue_view', ['id' => $issue->getId()]);
    }
}
