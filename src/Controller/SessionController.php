<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Categorie;
use App\Entity\Programme;
use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\SessionRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
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
        if($form['nb_places']->getData() < $session->nbPlacesReservees()){
          $this->addFlash(
            'error',
            'Le nombre de places doit être supérieur ou égale à '.$session->nbPlacesReservees()
          );
          return $this->redirectToRoute('edit_session', ['id'=>$session->getId()]);
        }
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

    #[Route('/session/{idSe}/{idSt}/delStagiaire', name: 'del_session_stagiaire')]
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

    #[Route('/session/{idSe}/{idSt}/addStagiaire', name: 'add_session_stagiaire')]
    #[ParamConverter('session', options: ['mapping' => ['idSe' => 'id']])]
    #[ParamConverter('stagiaire', options: ['mapping' => ['idSt' => 'id']])]
    public function addStagiaire(Stagiaire $stagiaire,Session $session,ManagerRegistry $doctrine): Response
    {
      if($session->isComplet())
      {
        $this->addFlash(
          'error',
          'La session est déjà complète !'
        );
      }
      else
      {
        $session->addStagiaire($stagiaire);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($session);
        $entityManager->flush();
      }
      return $this->redirectToRoute('show_session',['id'=>$session->getId()]);
    }

    #[Route('/session/{idSe}/{idMo}/addModule', name: 'add_session_module')]
    #[ParamConverter('session', options: ['mapping' => ['idSe' => 'id']])]
    #[ParamConverter('module', options: ['mapping' => ['idMo' => 'id']])]
    public function addModule(Module $module,Session $session,ManagerRegistry $doctrine): Response
    {
      if(isset($_POST['submit']))
      {
        $entityManager = $doctrine->getManager();
        $programme = new Programme();
        $nb_jour = filter_input(INPUT_POST,'nbJours',FILTER_VALIDATE_INT);

        $programme->setNbJours($nb_jour);
        $programme->setModule($module);
        $entityManager->persist($programme);
        $session->addProgramme($programme);

        $entityManager->persist($session);
        $entityManager->flush();
        return $this->redirectToRoute('show_session',['id'=>$session->getId()]);
      }
    }

    #[Route('/session/{idSe}/{idPr}/delModule', name: 'del_session_prog')]
    #[ParamConverter('session', options: ['mapping' => ['idSe' => 'id']])]
    #[ParamConverter('programme', options: ['mapping' => ['idPr' => 'id']])]
    public function delModule(Programme $programme,Session $session,ManagerRegistry $doctrine): Response
    {
      $entityManager = $doctrine->getManager();
      $entityManager->remove($programme);
      $entityManager->flush();
      return $this->redirectToRoute('show_session',['id'=>$session->getId()]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, SessionRepository $sr): Response
    {
      $nonRegistered = $sr->findNonRegistered($session->getId());
      $nonProgrammes = $sr->findNonProgrammes($session->getId());
      return $this->render('session/show.html.twig', [
        'session' => $session,
        'nonRegistered' => $nonRegistered,
        'nonProgrammes' => $nonProgrammes,
      ]);
    }
}
