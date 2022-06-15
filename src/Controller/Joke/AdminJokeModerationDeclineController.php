<?php

namespace App\Controller\Joke;

use App\Entity\JokeModeration;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminJokeModerationDeclineController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/moderate/{id}/decline', name: 'app_jokes_decline')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function declineJoke(JokeModeration $joke)
    {
        $this->entityManager->remove($joke);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke->getId()
        ]);
    }

}
