<?php

namespace BugTrackBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BugTrackBundle\Entity\Project;
use BugTrackBundle\Form\Type\ProjectFormType;

/**
 * Class ProjectController
 * @package BugTrackBundle\Controller
 */
class ProjectController extends Controller
{
    /**
     * Project view
     * 
     * @Route("/project/view/{id}", name="project_view")
     * @ParamConverter("project", class="BugTrackBundle:Project")
     * @Method({"GET"})
     * @Template("project/view.html.twig")
     *
     * @param Project $project
     * @return Response
     */
    public function viewAction(Project $project)
    {
        //ToDo: add permissions & voter later

        return [
            'project' => $project,
        ];
    }

    /**
     * Project create
     * 
     * @Route("/project/create", name="project_create")
     * @Method({"GET", "POST"})
     * @Template("project/edit.html.twig")
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        //ToDo: add permissions & voter later
        
        $project = new Project();
        $pageLabel = $this->get('translator')->trans('project.create', [], 'BugTrackBundle');
        $form = $this->createForm(ProjectFormType::class, $project, ['label' => $pageLabel]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_view', ['id' => $project->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
        ];
    }

    /**
     * Project edit
     *
     * @Route("/project/edit/{id}", name="project_edit")
     * @ParamConverter("project", class="BugTrackBundle:Project")
     * @Method({"GET", "POST"})
     * @Template("project/edit.html.twig")
     *
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function editAction(Project $project, Request $request)
    {
        //ToDo: add permissions & voter later

        $pageLabel = $this->get('translator')->trans('project.edit', [], 'BugTrackBundle');
        $form = $this->createForm(ProjectFormType::class, $project, ['label' => $pageLabel]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('project_view', ['id' => $project->getId()]);
        }

        return [
            'form' => $form->createView(),
            'pageTitle' => $pageLabel,
            'project' => $project,
        ];
    }
}
