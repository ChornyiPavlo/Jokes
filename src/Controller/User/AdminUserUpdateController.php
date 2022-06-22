<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\AdminUserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminUserUpdateController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private ManagerRegistry $doctrine;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager,
        ManagerRegistry             $doctrine
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    #[Route('/users', name: 'app_users')]
    #[IsGranted("ROLE_ADMIN")]
    public function ListUsers(): Response
    {
        $loadUsers = $this->doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'loadUsers' => $loadUsers,
        ]);
    }

    #[Route('/users/{id}/update', name: 'app_admin_user_update')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function UpdateUser(User $user, Request $request)
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_users', [
                'id' => $user->getId()
            ]);
        }
        return $this->render('admin/updateuser.html.twig', [
            'loadUser' => $form->createView()
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function Delete(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_users', [
            'id' => $user->getId()
        ]);
    }
}