<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $sessions = $doctrine->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/session/add', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response
    {
      if(!$session){
        $session = new Session();
      }
      $form = $this->createForm(SessionType::class, $session);
      $form->handleRequest($request);
      
      // Traitement du formulaire
      // isValid = filterInput
      if($form->isSubmitted() && $form->isValid()){
        //objet session hydrate par les donnees du formulaire
        $session = $form->getData();

        // Manager de doctrine, permet d'acceder au persist et au flush
        $entityManager = $doctrine->getManager();
        // Prepare objet 
        $entityManager->persist($session);
        // Execute = Insert Into 
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
      }

      return $this->render('session/add.html.twig',[
        'formSession' => $form->createView(),
        'edit' => $session->getId()
      ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session): Response
    {
      $dateformat = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
      $dateformat->setPattern('d MMMM Y');

      return $this->render('session/show.html.twig', [
        'session' => $session,
        'date' => $dateformat->format($session->getDateDebut()),
        'programmes' => $session->getProgrammes()
      ]);
    }
}
