<?php

namespace App\Controller\Joke;

use App\Entity\Joke;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminJokeDeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/jokes/{id}/delete', name: 'app_jokes_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function deleteJoke(Joke $joke)
    {
        $this->entityManager->remove($joke);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_jokes', [
            'id' => $joke->getId()
        ]);
    }
}
