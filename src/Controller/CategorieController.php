<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(ManagerRegistry $doctrine): Response
    {
      $categories = $doctrine->getRepository(Categorie::class)->findAll();
      return $this->render('categorie/index.html.twig',[
        'categories' => $categories,
      ]);
    }

    #[Route('/categorie/add', name: 'add_categorie')]
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
    {
        if(!$categorie)
        {
          $categorie = new Categorie();
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        
        // Traitement du formulaire
        // isValid = filterInput
        if($form->isSubmitted() && $form->isValid()){
          //objet session hydrate par les donnees du formulaire
          $categorie = $form->getData();
  
          // Manager de doctrine, permet d'acceder au persist et au flush
          $entityManager = $doctrine->getManager();
          // Prepare objet 
          $entityManager->persist($categorie);
          // Execute = Insert Into 
          $entityManager->flush();
  
          return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/add.html.twig',[
          'formCategorie' => $form->createView(),
          'edit' => $categorie->getId()
        ]);
    }

    #[Route('/categorie/{id}/delete', name: 'del_categorie')]
    public function delCategorie(Categorie $categorie, ManagerRegistry $doctrine): Response
    {
      // Manager de doctrine, permet d'acceder au persist et au flush
      $entityManager = $doctrine->getManager();
      $entityManager->remove($categorie);
      $entityManager->flush();

      return $this->redirectToRoute('app_categorie');
    }
}
