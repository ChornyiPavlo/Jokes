<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminUserDeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface      $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function delete(User $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_users', [
            'id' => $user->getId()
        ]);
    }
}