<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Categorie;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use Doctrine\ORM\Mapping\OrderBy;
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

    #[Route('/session/{id}/stagiaire', name: 'del_stagiaire')]
    public function del(Stagiaire $stagiaire): Response
    {
      // $modules = $doctrine->getRepository(Module::class)->findBy([],["categorie" => 'ASC']);
      $delstagaire = $stagiaire->getRepository(Stagiaire::class)->remove();

      return $this->render('session/show.html.twig', [
        'session' => $session,
        'categories' => $categories,
      ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, ManagerRegistry $doctrine): Response
    {
      // $modules = $doctrine->getRepository(Module::class)->findBy([],["categorie" => 'ASC']);
      $modules = $doctrine->getRepository(Module::class)->groupBy();
      $categories = $doctrine->getRepository(Categorie::class)->findAll();

      return $this->render('session/show.html.twig', [
        'session' => $session,
        'categories' => $categories,
      ]);
    }
}
