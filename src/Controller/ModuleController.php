<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Form\ModuleType;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $modules = $doctrine->getRepository(Module::class)->findAll();
        $categories = $doctrine->getRepository(Categorie::class)->findAll();
        return $this->render('module/index.html.twig',[
          'modules' => $modules,
          'categories' => $categories,
        ]);
    }

    #[Route('/module/add', name: 'add_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]
    public function add(ManagerRegistry $doctrine, Module $module = null, Request $request): Response
    {
        if(!$module)
        {
          $module = new Module();
        }
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);
        
        // Traitement du formulaire
        // isValid = filterInput
        if($form->isSubmitted() && $form->isValid()){
          //objet session hydrate par les donnees du formulaire
          $module = $form->getData();
  
          // Manager de doctrine, permet d'acceder au persist et au flush
          $entityManager = $doctrine->getManager();
          // Prepare objet 
          $entityManager->persist($module);
          // Execute = Insert Into 
          $entityManager->flush();
  
          return $this->redirectToRoute('app_module');
        }
        return $this->render('module/add.html.twig',[
          'formModule' => $form->createView(),
          'edit' => $module->getId()
        ]);
    }

}
