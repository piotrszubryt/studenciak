<?php

namespace Studenciak\StudentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
		return $this->render('StudenciakBundle:Page:extend/profile.html.twig');
	}

	public function logoutAction()
	{
		return $this->render('StudenciakBundle:Page:extend/logout.html.twig');
	}

}
