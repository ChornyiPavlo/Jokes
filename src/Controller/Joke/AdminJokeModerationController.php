<?php

namespace App\Controller\Joke;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Entity\JokeModeration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminJokeModerationController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry        $doctrine,
        EntityManagerInterface $entityManager,
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
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

    #[Route('/moderate/{id}/approve', name: 'app_jokes_approve')]
    #[IsGranted("ROLE_ADMIN")]
    public function ApproveJoke(Request $request)
    {
        $idJoke = $request->attributes->get('id');
        /** @var JokeModeration $jokeModeration */
        $jokeModeration = $this->doctrine->getRepository(JokeModeration::class)->find($idJoke);
        if (null === $jokeModeration) {
            throw new NotFoundHttpException();
        }

        $joke = new Joke();
        $joke->setCategory($jokeModeration->getCategory());
        $joke->setJoke($jokeModeration->getJoke());

        $this->entityManager->persist($joke);
        $this->entityManager->remove($jokeModeration);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke->getId()
        ]);
    }

    #[Route('/moderate/{id}/decline', name: 'app_jokes_decline')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function DeclineJoke(JokeModeration $joke)
    {
        $this->entityManager->remove($joke);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke
        ]);
    }
}
