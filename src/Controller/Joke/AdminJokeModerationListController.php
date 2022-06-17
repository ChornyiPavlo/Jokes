<?php

namespace App\Controller\Joke;

use App\Entity\Categories;
use App\Entity\JokeModeration;
use App\Repository\CategoriesRepository;
use App\Repository\JokeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminJokeModerationListController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine,
    )
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/moderate', name: 'app_jokes_moderate')]
    #[IsGranted("ROLE_ADMIN")]
    public function ModerateListJokes(): Response
    {
        $loadJoke = $this->doctrine->getRepository(JokeModeration::class)->findAll();
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/moderatejokes.html.twig', [
            'loadJoke' => $loadJoke,
            'loadCategory' => $loadCategory,
        ]);
    }
}
