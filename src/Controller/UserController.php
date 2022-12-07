<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    // DELETE USER
    #[Route('/{id}/delete', name:'user_del')]
    public function delete(User $user,ManagerRegistry $doctrine)
    {
      $user->setRoles(['ROLE_USER']);
      $entityManager = $doctrine->getManager();
      $entityManager->remove($user);
      $entityManager->flush();
      return $this->redirectToRoute('app_user');
    }

    // ADMIN Promotion
    #[Route('/{id}/promote', name:'promote_admin')]
    public function promoteAdmin(User $user,ManagerRegistry $doctrine)
    {
      $user->setRoles(['ROLE_ADMIN']);
      $entityManager = $doctrine->getManager();
      $entityManager->persist($user);
      $entityManager->flush();
      return $this->redirectToRoute('app_user');
    }

    // ADMIN dePromotion
    #[Route('/{id}/unpromote', name:'unpromote_admin')]
    public function unAdmin(User $user,ManagerRegistry $doctrine)
    {
      $user->setRoles(['ROLE_USER']);
      $entityManager = $doctrine->getManager();
      $entityManager->persist($user);
      $entityManager->flush();
      return $this->redirectToRoute('app_session');
    }
    
}
