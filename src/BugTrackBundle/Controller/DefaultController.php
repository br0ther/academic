<?php

namespace BugTrackBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @Template("default/home.html.twig")
     *
     * @return Response
     */
    public function indexAction()
    {
        $userIssues = $this->getDoctrine()->getRepository('BugTrackBundle:Issue')
            ->getIssuesForMainPageQB($this->getUser())
            ->getQuery()
            ->getResult();

        return [
            'projects' => [],
            'issues' => $userIssues,
        ];
    }
}
