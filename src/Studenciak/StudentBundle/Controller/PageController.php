<?php

namespace Studenciak\StudentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class PageController extends Controller
{
    //public function indexAction($name)
   // {
        //return $this->render('StudenciakBundle:Page:index.html.twig', array('name' => $name));
   // }

	public function indexAction()
	{
		return $this->render('StudenciakBundle:Page:base.html.twig');
	}

	public function courseAction()
	{
		return $this->render('StudenciakBundle:Page:extend/course.html.twig');
	}

	public function repoAction()
	{
		return $this->render('StudenciakBundle:Page:extend/repo.html.twig');
	}

	public function groupsAction()
	{
		return $this->render('StudenciakBundle:Page:extend/groups.html.twig');
	}

	public function diaryAction()
	{
		return $this->render('StudenciakBundle:Page:extend/diary.html.twig');
	}

	public function profileAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('name'))  
			return $this->redirect($this->generateUrl('course'));

		return $this->render('StudenciakBundle:Page:extend/profile.html.twig');
	}

	public function logoutAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('name'))  
		return $this->redirect($this->generateUrl('login'));

		return $this->render('StudenciakBundle:Page:extend/logout.html.twig');
	}

	public function loginAction()
	{
		$session = $this->getRequest()->getSession();
		if ($session->get('name'))  
			return $this->redirect($this->generateUrl('profile'));

		return $this->render('StudenciakBundle:Page:extend/login.html.twig');
	}

	public function AjaxUpdateDataAction()
	{
		$request = $this->container->get('request');        
		$id = $request->request->get('id');
		$name = $request->request->get('name');
		$image = $request->request->get('image');
		$email = $request->request->get('email');

		$response = array("code" => $name);


		$session = $this->getRequest()->getSession();
		$session->set('id', $id);
		$session->set('name', $name);
		$session->set('image', $image);
		$session->set('email', $email);


		return new Response(json_encode($response)); 
	}

	public function logoutSessionAction()
	{
		$session = $this->getRequest()->getSession();
		if ($session->get('name'))  
		{
			$session->remove('id');
			$session->remove('name');
			$session->remove('image');
			$session->remove('email');
		}
		return $this->redirect($this->generateUrl('course'));
	}

}
