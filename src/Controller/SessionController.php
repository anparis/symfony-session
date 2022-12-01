<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Categorie;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // $sessions = $doctrine->getRepository(Session::class)->findAll();

        $sessionsPast = $doctrine->getRepository(Session::class)->sessionsPast();
        $sessionsCurrent = $doctrine->getRepository(Session::class)->sessionsCurrent();
        $sessionsFuture = $doctrine->getRepository(Session::class)->sessionsFuture();

        return $this->render('session/index.html.twig', [
            // 'sessions' => $sessions,
            'sessionsPast' => $sessionsPast,
            'sessionsCurrent' => $sessionsCurrent,
            'sessionsFuture' => $sessionsFuture,
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

    #[Route('/session/{id}/delete', name: 'del_session')]
    public function delSession(Session $session, ManagerRegistry $doctrine): Response
    {
      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      $entityManager->remove($session);
      $entityManager->flush();

      return $this->redirectToRoute('app_session');
    }

    #[Route('/session/{idSe}/{idSt}/delStagiaire', name: 'del_stagiaire')]
    #[ParamConverter('session', options: ['mapping' => ['idSe' => 'id']])]
    #[ParamConverter('stagiaire', options: ['mapping' => ['idSt' => 'id']])]
    public function delStagiaire(Stagiaire $stagiaire,Session $session,ManagerRegistry $doctrine): Response
    {
      $session->removeStagiaire($stagiaire);
      $entityManager = $doctrine->getManager();
      $entityManager->persist($session);
      $entityManager->flush();
      return $this->redirectToRoute('show_session',['id'=>$session->getId()]);
    }

    #[Route('/session/{id}/addStagiaire', name: 'add_stagiaire')]
    public function addStagiaire(Stagiaire $stagiaire,Session $session,ManagerRegistry $doctrine): Response
    {
      $session->addStagiaire($stagiaire);
      $entityManager = $doctrine->getManager();
      $entityManager->persist($session);
      $entityManager->flush();
      return $this->redirectToRoute('show_session',['id'=>$session->getId()]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, ManagerRegistry $doctrine, SessionRepository $sr): Response
    {
      $categories = $doctrine->getRepository(Categorie::class)->findAll();
      $nonRegistered = $sr->findNonRegistered($session->getId());

      return $this->render('session/show.html.twig', [
        'session' => $session,
        'nonRegistered' => $nonRegistered,
        'categories' => $categories,
      ]);
    }
}
