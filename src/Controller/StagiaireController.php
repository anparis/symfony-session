<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
  #[Route('/stagiaire', name: 'app_stagiaire')]
  public function index(ManagerRegistry $doctrine): Response
  {
    $stagiaires = $doctrine->getRepository(Stagiaire::class)->findAll();
    return $this->render('stagiaire/index.html.twig', [
      'stagiaires' => $stagiaires
    ]);
  }

  #[Route('/stagiaire/add', name: 'add_stagiaire')]
  #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
  public function add(ManagerRegistry $doctrine, Stagiaire $stagiaire = null, Request $request): Response
  {
    if (!$stagiaire) {
      $stagiaire = new Stagiaire();
    }
    $form = $this->createForm(StagiaireType::class, $stagiaire);
    $form->handleRequest($request);

    // Traitement du formulaire
    // isValid = filterInput
    if ($form->isSubmitted() && $form->isValid()) {
      //objet stagiaire hydrate par les donnees du formulaire
      $stagiaire = $form->getData();

      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      // Prepare objet 
      $entityManager->persist($stagiaire);
      // Execute = Insert Into 
      $entityManager->flush();

      return $this->redirectToRoute('app_stagiaire');
    }

    return $this->render('stagiaire/add.html.twig', [
      'formStagiaire' => $form->createView(),
      'edit' => $stagiaire->getId()
    ]);
  }
}
