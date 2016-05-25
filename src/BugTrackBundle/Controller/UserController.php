<?php

namespace BugTrackBundle\Controller;

use BugTrackBundle\Entity\User;
use BugTrackBundle\Form\Type\UserFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package BugTrackBundle\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/user/edit/{id}", name="user_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template("user/edit.html.twig")
     *
     * @param Request $request
     * @param number  $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        /** @var User $user */
        if (empty($id)) {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getRepository('BugTrackBundle:User')->findOneById($id);
        }

        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        //ToDo: add permissions & voter later

        $form = $this->createForm(new UserFormType(), $user, ['label' => 'Edit profile']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_view', ['id' => $user->getId()]);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/user/view/{id}", name="user_view", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @Template("user/view.html.twig")
     *
     * @param number $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id = null)
    {
        $userRepository = $this->getDoctrine()->getRepository('BugTrackBundle:User');

        /** @var User $user */
        if (empty($id)) {
            $user = $this->getUser();
        } else {
            $user = $userRepository->findOneById($id);
        }

        if (empty($user)) {
            throw $this->createNotFoundException('User not found');
        }

        //ToDo: add permissions
        
        return [
            'user' => $user,
            'user_roles' => implode(', ', $user->getRolesNames()),
        ];
    }
}
