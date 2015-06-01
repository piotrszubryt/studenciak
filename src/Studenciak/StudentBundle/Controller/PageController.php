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
    	return $this->render('StudenciakBundle:Page:dashboard.html.twig');
    }

    public function courseAction()
    {
    	return $this->render('StudenciakBundle:Page:dashboard.html.twig');
    }

}
