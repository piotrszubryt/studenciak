<?php

namespace Studenciak\StudentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Studenciak\StudentBundle\Entity\Osoba;
use Studenciak\StudentBundle\Entity\Przedmiot;
use Studenciak\StudentBundle\Entity\Lekcje;
use Studenciak\StudentBundle\Entity\Zajecia;
use Studenciak\StudentBundle\Entity\OsobaPrzedmiot;
use Studenciak\StudentBundle\Entity\OsobaZajecia;
use Studenciak\StudentBundle\Entity\Repozytorium;

class PageController extends Controller
{

	public function zobacz($co)
	{
		echo '<pre>';
		\Doctrine\Common\Util\Debug::dump($co);
		echo '</pre>';
	}


	public function indexAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));
		else
			return $this->redirect($this->generateUrl('przedmiot'));
	}

	public function przedmiotAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));

		$em = $this->getDoctrine()->getRepository('StudenciakBundle:Przedmiot');
		$wszystkie_przedmioty = $em->findBy(array(), array('semestr'=>'ASC', 'nazwa'=>'ASC'));

		$em = $this->getDoctrine()->getRepository('StudenciakBundle:OsobaPrzedmiot');
		$moje_przedmioty = $em->findBy(array('id_osoby'=>$session->get('id')));

		$moje_przedmioty_ob = array();
		foreach ($moje_przedmioty as $przed) {					//wyciągamy przedmioty z tablicy obiektow OsobaPrzedmiot
			$moje_przedmioty_ob[] = $przed->getIdPrzedmiotu();
		}

		$przedmioty = array();
		foreach ($wszystkie_przedmioty as $przed) {				//usuwamy przedmioty na ktorych jestesmy
			if (!(in_array($przed, $moje_przedmioty_ob)))
				$przedmioty[] = $przed;
		}

		return $this->render('StudenciakBundle:Page:extend/przedmiot.html.twig', array('moje_przedmioty' => $moje_przedmioty_ob, 'przedmioty' => $przedmioty));
	}

	public function grupyAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));

		return $this->render('StudenciakBundle:Page:extend/grupy.html.twig');
	}

	public function osobyAction()
	{
		$session = $this->getRequest()->getSession();
		if ($session->get('admin'))
		{
			$em = $this->getDoctrine()->getRepository('StudenciakBundle:Osoba');
			$osoby = $em->findBy(array(), array('email'=>'ASC'));

			return $this->render('StudenciakBundle:Page:extend/osoby.html.twig', array('osoby' => $osoby));
		}
		else
		{
			return $this->redirect($this->generateUrl('login'));
		}
	}

	public function dziennikAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));

		return $this->render('StudenciakBundle:Page:extend/dziennik.html.twig');
	}

	public function profilAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('course'));

		return $this->render('StudenciakBundle:Page:extend/profil.html.twig');
	}

	public function logoutAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));

		return $this->render('StudenciakBundle:Page:extend/logout.html.twig');
	}

	public function loginAction()
	{
		$session = $this->getRequest()->getSession();
		if ($session->get('email'))
			return $this->redirect($this->generateUrl('profil'));

		return $this->render('StudenciakBundle:Page:extend/login.html.twig');
	}

	public function loginAjaxAction()
	{
		$request = $this->container->get('request');
		$name = $request->request->get('name');
		$image = $request->request->get('image');
		$email = $request->request->get('email');

		$em = $this->getDoctrine()->getManager();

		$akceptowany = $this->getDoctrine()->getRepository('StudenciakBundle:Osoba')->findOneByEmail($email);

		if (!$akceptowany)
		{
			$response = array("code" => 'niezarejestrowany');

			$osoba = new Osoba();

			$osoba->SetNazwisko($name);
			$osoba->SetEmail($email);
			$osoba->SetZdjecie($image);
			$osoba->SetAdmin(0);
			$osoba->SetAktywny(0);

			$em->persist($osoba);
			$em->flush();
		}

		else
		{
			if ($akceptowany->getAktywny() == 0)
				$response = array("code" => 'niezaakceptowany');
			else
			{
				$response = array("code" => 'akceptowany');

				/// uaktualnienie danych 
				if (($akceptowany->getNazwisko() != $name) || ($akceptowany->getZdjecie() != $image))
				{
					$akceptowany->SetZdjecie($image); 
					$akceptowany->setNazwisko($name);
					$em->flush();
				}



				$session = $this->getRequest()->getSession();
				$session->set('name', $name);
				$session->set('image', $image);
				$session->set('email', $email);
				$session->set('admin', $akceptowany->getAdmin());
				$session->set('id', $akceptowany->getIdOsoby());

			}
		}
		return new Response(json_encode($response));
	}

	public function logoutSessionAction()
	{
		$session = $this->getRequest()->getSession();
		if ($session->get('email'))
		{
			$session->remove('name');
			$session->remove('image');
			$session->remove('email');
			$session->remove('admin');
		}
		return $this->redirect($this->generateUrl('przedmiot'));
	}

	public function osobaUsunAction($id)
	{
		$repo = $this->getDoctrine()->getRepository('StudenciakBundle:Osoba');
		$osoba = $repo->find($id);

		return $this->render('StudenciakBundle:Page:extend/usun.html.twig', array('osoba' => $osoba));
	}

	public function osobaUsuwanieAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $this->getDoctrine()->getRepository('StudenciakBundle:Osoba');
		$osoba = $repo->find($id);
		$em->remove($osoba);
		$em->flush();

		return $this->redirect($this->generateUrl('osoby'));
	}

	public function osobaAdminAction($tryb, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$osoba = $em->getRepository('StudenciakBundle:Osoba')->find($id);

		if (!$osoba) {
			return $this->redirect($this->generateUrl('osoby'));
		}

		$osoba->setAdmin($tryb);
		$em->flush();

		return $this->redirect($this->generateUrl('osoby'));
	}

	public function aktywujAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$osoba = $em->getRepository('StudenciakBundle:Osoba')->find($id);

		if (!$osoba) {
			return $this->redirect($this->generateUrl('osoby'));
		}

		$osoba->setAktywny(1);
		$em->flush();

		return $this->redirect($this->generateUrl('osoby'));
	}

	public function przedmiotDodajAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$session = $this->getRequest()->getSession();
		if ($session->get('admin'))
		{
			$osoba = $em->getRepository('StudenciakBundle:Osoba')->findOneByEmail($session->get('email'));	//pobieramy siebie z bazy

			$przedmiot = new Przedmiot();
			$przedmiot->setIdOsoby($osoba);			//dodajemy siebie jako prowadzącego przedmiot

			$form = $this->createFormBuilder($przedmiot)
			->add('nazwa', 'text', array('label'  => 'Nazwa przedmiotu', 'max_length' => 255))
			->add('haslo', 'text', array('label'  => 'Hasło do przedmiotu', 'max_length' => 255))
			->add('semestr', 'integer', array('label'  => 'Semestr', 'data' => 1, 'attr' => array('min' => '1', 'max' => '8')))
			->getForm();

			$form->handleRequest($request);
			if ($form->isValid()) {

				$task = $form->getData();
				$em->persist($task);
				$em->flush();

				return $this->redirect($this->generateUrl('przedmiotPokaz', array('id' => $przedmiot->getIdPrzedmiotu())));
			}

			return $this->render('StudenciakBundle:Page:extend/przedmiotDodaj.html.twig', array('form' => $form->createView()));
		}

		else
			return $this->redirect($this->generateUrl('przedmiot'));
	}

	public function przedmiotPokazAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();

		$przedmiot = $em->getRepository('StudenciakBundle:Przedmiot')->find($id);
		
		$czy_zapisany = $this->getDoctrine()->getRepository('StudenciakBundle:OsobaPrzedmiot')
		->findBy(array('id_osoby'=>$session->get('id'), 'id_przedmiotu'=>$id));

		if (empty($czy_zapisany)) {
			return $this->redirect($this->generateUrl('przedmiotZapiszSie', array('id' => $id)));
		}

		$zajecia = $em->getRepository('StudenciakBundle:Zajecia')->findBy(array('id_przedmiotu' => $id));

		return $this->render('StudenciakBundle:Page:extend/przedmiotPokaz.html.twig', array('przedmiot'=>$przedmiot, 'zajecia' => $zajecia));
	}


	public function przedmiotZapiszSieAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();

		$przedmiot = $em->getRepository('StudenciakBundle:Przedmiot')->find($id);
		$osoba = $em->getRepository('StudenciakBundle:Osoba')->findOneByEmail($session->get('email'));	//pobieramy siebie z bazy

		$bledne_haslo = "";

		$przedmiot_form = new Przedmiot();
		$form = $this->createFormBuilder($przedmiot_form)
		->add('haslo', 'text', array('label'  => 'Hasło do przedmiotu', 'max_length' => 255))
		->getForm();

		$form->handleRequest($request);
		if ($form->isValid()) {

			$haslo = $form->get('haslo')->getData();
			$this->zobacz($haslo);
			$this->zobacz($przedmiot->getHaslo());
			if ($haslo == $przedmiot->getHaslo())
			{
				$osoba_przedmiot = new OsobaPrzedmiot();
				$osoba_przedmiot->setIdOsoby($osoba);
				$osoba_przedmiot->setIdPrzedmiotu($przedmiot);

				$em->persist($osoba_przedmiot);
				$em->flush();

				return $this->redirect($this->generateUrl('przedmiotPokaz', array('id' => $id)));
			}
			else
			{
				$bledne_haslo = "Podane hasło jest nieprawidłowe";
			}

		}

		return $this->render('StudenciakBundle:Page:extend/przedmiotZapiszSie.html.twig', 
			array('form' => $form->createView(), 'przedmiot'=>$przedmiot, 'bledne_haslo' => $bledne_haslo));


	}


	public function przedmiotDodajZajeciaAction(Request $request, $id_przedmiotu)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();

		if ($session->get('admin'))
		{
			$przedmiot = $em->getRepository('StudenciakBundle:Przedmiot')->find($id_przedmiotu);
			$nauczyciele = $em->getRepository('StudenciakBundle:Osoba')->findBy(array('admin' => 1));	//pobieramy nauczycieli

			$wybor_nauczycieli = new ObjectChoiceList($nauczyciele, 'nazwisko', array(), null, 'id_osoby');	//lista wyboru nauczycieli

			$zajecia = new Zajecia();
			$zajecia->setIdPrzedmiotu($przedmiot);			//id przedmiotu do ktorego dodajemy

			$form = $this->createFormBuilder($zajecia)
			->add('id_osoby', 'choice', array('label'  => 'Prowadzący', 
				'choice_list' => $wybor_nauczycieli))
			->add('typ_zajec', 'choice', array('label'  => 'Typ zajęć', 
				'choices' => array('w' => 'wykład','c' => 'ćwiczenia', 'l' => 'laboratorium', 'p'=> 'projekt')))
			->add('sala', 'text', array('label'  => 'Sala', 'max_length' => 45))
			->add('termin', 'datetime', array('label'  => 'Pierwsze zajęcia'))
			->getForm();



			$form->handleRequest($request);
			if ($form->isValid()) {

				$task = $form->getData();
				$em->persist($task);
				$em->flush();

				return $this->redirect($this->generateUrl('przedmiotPokaz', array('id' => $id_przedmiotu)));
			}

			return $this->render('StudenciakBundle:Page:extend/przedmiotDodajZajecia.html.twig', array('form' => $form->createView()));
		}

		else
			return $this->redirect($this->generateUrl('przedmiot'));
	}

	public function zajeciaPokazAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();
		
		$zajecia = $em->getRepository('StudenciakBundle:Zajecia')->find($id);
		$lekcje = $em->getRepository('StudenciakBundle:Lekcje')->findBy(array('id_zajec' => $id), array('data_lekcji' => 'ASC'));		

		$czy_zapisany = $this->getDoctrine()->getRepository('StudenciakBundle:OsobaZajecia')
		->findBy(array('id_osoby'=>$session->get('id'), 'id_zajec'=>$id));

		return $this->render('StudenciakBundle:Page:extend/zajeciaPokaz.html.twig', 
			array('zajecia' => $zajecia, 'lekcje' => $lekcje, 'zapisany' => $czy_zapisany));
	}

	public function zajeciaDodajAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();

		$session = $this->getRequest()->getSession();
		if ($session->get('admin'))
		{
			$zajecia = $em->getRepository('StudenciakBundle:Zajecia')->find($id);
			$lekcje = new Lekcje();
			$lekcje->setIdZajec($zajecia);

			$data_zajec = new \DateTime($zajecia->getTermin()->format('Y-m-d H:i:s'));

			$form = $this->createFormBuilder($lekcje)
			->add('temat', 'text', array('label'  => 'Temat zajęć', 'max_length' => 255))
			->add('data_lekcji', 'date', array('label'  => 'Data zajęć',  'data' => $data_zajec))
			->add('kolejne', 'integer', array('label'  => 'Powtórzenie zajęć w następnych tygodniach', 'data' => 0,
				'mapped' => false, 'attr' => array('min' => '0', 'max' => '15')))
			->getForm();

			$form->handleRequest($request);
			if ($form->isValid()) 
			{

				$z = $form->get('kolejne')->getData();
				$data_zajec = $form->get('data_lekcji')->getData();

				$task = $form->getData();
				$em->persist($task);
				$em->flush();

				if ($z)
				{
					for ($i=1; $i <= $z; $i++) { 
						$lekcje_tmp = new Lekcje();
						$lekcje_tmp->setIdZajec($zajecia);

						$lekcje_tmp->setTemat('Brak tematu');
						
						$data_nowa = $data_zajec;
						$data_nowa->add(new \DateInterval('P7D'));

						$lekcje_tmp->setDataLekcji($data_nowa);
						$em->persist($lekcje_tmp);
						$em->flush();
					}
				}

				return $this->redirect($this->generateUrl('zajeciaPokaz', array('id' => $id)));
			}

			return $this->render('StudenciakBundle:Page:extend/zajeciaDodaj.html.twig', array('form' => $form->createView()));
		}

		else
			return $this->redirect($this->generateUrl('przedmiot'));
	}


	public function zajeciaZapiszSieAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();

		$zajecia = $em->getRepository('StudenciakBundle:Zajecia')->find($id);
		$osoba = $em->getRepository('StudenciakBundle:Osoba')->findOneByEmail($session->get('email'));	//pobieramy siebie z bazy


		$osoba_zajecia = new OsobaZajecia();
		$osoba_zajecia->setIdOsoby($osoba);
		$osoba_zajecia->setIdZajec($zajecia);

		$em->persist($osoba_zajecia);
		$em->flush();

		return $this->redirect($this->generateUrl('zajeciaPokaz', array('id' => $id)));


	}
// ---------------------------------------------------------------------- REPOZYTORIA ------------------------------------------------------------------- 
	public function repoAction()
	{
		$session = $this->getRequest()->getSession();
		if (!$session->get('email'))
			return $this->redirect($this->generateUrl('login'));

		$em = $this->getDoctrine()->getRepository('StudenciakBundle:Repozytorium');
		$wszystkie_repozytoria = $em->findAll();

		$em = $this->getDoctrine()->getRepository('StudenciakBundle:Repozytorium');
		$moje_repozytoria = $em->findBy(array('id_osoby'=>$session->get('id')));

		$repozytoria = array();
		foreach ($wszystkie_repozytoria as $rrepo) {				//usuwamy repozytoria na ktorych jestesmy
			if (!(in_array($rrepo, $moje_repozytoria)))
				$repozytoria[] = $rrepo;
		}
		

		

		$uczen_przemioty = $this->getDoctrine()->getRepository('StudenciakBundle:OsobaPrzedmiot')->findBy(array('id_osoby'=>$session->get('id'))); // pobieram przemioty na ktorych zapisany jest uczen

		$em = $this->getDoctrine()->getRepository('StudenciakBundle:Repozytorium');
		$uczen_repozytoria = array();
		$uczen_repozytoria = $em->findBy(array('id_przedmiotu'=>$uczen_przemioty[0]->getIdPrzedmiotu()));



		return $this->render('StudenciakBundle:Page:extend/repo.html.twig', array('moje_repozytoria' => $moje_repozytoria, 'repozytoria' => $repozytoria, 'uczen_repozytoria' => $uczen_repozytoria));
	}

public function repoDodajAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $this->getRequest()->getSession();

		if ($session->get('admin'))
		{

			$osoba = $em->getRepository('StudenciakBundle:Osoba')->findOneByEmail($session->get('email'));	//pobieramy siebie z bazy
			$przedmioty = $em->getRepository('StudenciakBundle:Przedmiot')->findAll();	//pobieramy przedmioty
			$wybor_przedmiotu = new ObjectChoiceList($przedmioty, 'nazwa', array(), null, 'id_przedmiotu');	//lista wyboru pzedmiotow

			$repozytoria = new Repozytorium();
			$repozytoria->setIdOsoby($osoba);			//id przedmiotu do ktorego dodajemy


			$form = $this->createFormBuilder($repozytoria)
			->add('id_przedmiotu', 'choice', array('label'  => 'Przedmiot', 
				'choice_list' => $wybor_przedmiotu))
			->add('nazwa', 'text', array('label'  => 'Nazwa', 'max_length' => 45))
			->add('file', 'file', array('label'  => 'Plik'))
			->getForm();

	    	if ($request->isMethod('POST')) {
	        	$form->submit($request);
	        	if ($form->isValid()) {
	            	$em = $this->getDoctrine()->getEntityManager();
	            	
	            	$em->persist($repozytoria);
	            	$em->flush();

	            	return $this->redirect($this->generateUrl('repo'));
	        	}
	    	}

	    	return $this->render('StudenciakBundle:Page:extend/repoDodaj.html.twig', array('form' => $form->createView()));

		}
		else
			return $this->redirect($this->generateUrl('repo'));

	}
	public function repoUsunAction($id)
	{
		$repo = $this->getDoctrine()->getRepository('StudenciakBundle:Repozytorium');
		$tmp = $repo->find($id);

		return $this->render('StudenciakBundle:Page:extend/repoUsun.html.twig', array('repo' => $tmp));
	}
	public function repoUsuwanieAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$tmp = $this->getDoctrine()->getRepository('StudenciakBundle:Repozytorium');
		$repozytoria = new Repozytorium();
		$repo = $tmp->find($id);
		$em->remove($repo);
		$repozytoria->rem();
		$em->flush();

		return $this->redirect($this->generateUrl('repo'));
	}
}



