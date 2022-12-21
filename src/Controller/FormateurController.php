<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $fr): Response
    {
        $formateurs = $fr->findAll();
        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }

    #[Route('/formateur/add', name: 'add_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function add(ManagerRegistry $doctrine, Formateur $formateur = null, Request $request): Response
    {
        if(!$formateur)
        {
          $formateur = new Formateur();
        }

        $form = $this->createForm(FormateurType::class, $formateur);
        $form->handleRequest($request);
        
        // Traitement du formulaire
        // isValid = filterInput
        if($form->isSubmitted() && $form->isValid()){
          //objet session hydrate par les donnees du formulaire
          $formateur = $form->getData();
  
          // Manager de doctrine, permet d'acceder au persist et au flush
          $entityManager = $doctrine->getManager();
          // Prepare objet 
          $entityManager->persist($formateur);
          // Execute = Insert Into 
          $entityManager->flush();
  
          return $this->redirectToRoute('app_formateur');
        }
        return $this->render('formateur/add.html.twig',[
          'formFormateur' => $form->createView(),
          'edit' => $formateur->getId()
        ]);
    }

    #[Route('/formateur/{id}/delete', name: 'del_formateur')]
    public function delformateur(Formateur $formateur, ManagerRegistry $doctrine): Response
    {
      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      $entityManager->remove($formateur);
      $entityManager->flush();

      return $this->redirectToRoute('app_formateur');
    }

    #[Route('/formateur/{id}/show', name: 'show_formateur')]
    public function showFormateur(Formateur $formateur)
    {
      return $this->render('formateur/show.html.twig', [
        'formateur' => $formateur,
        // 'sessions' => $sessions
      ]);
    }
}
