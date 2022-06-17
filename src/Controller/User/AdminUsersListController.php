<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminUsersListController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/users', name: 'app_users')]
    #[IsGranted("ROLE_ADMIN")]
    public function listUsers(): Response
    {
        $loadUsers = $this->doctrine->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'loadUsers' => $loadUsers,
        ]);
    }
}