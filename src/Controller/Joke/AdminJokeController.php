<?php

namespace App\Controller\Joke;

use App\CacheKeyDict;
use App\Entity\Categories;
use App\Entity\Joke;
use App\Form\AdminJokeType;
use App\Repository\JokeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route ('/admin')]
class AdminJokeController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private CacheInterface $cache;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry        $doctrine,
        CacheInterface         $cache,
        EntityManagerInterface $entityManager
    )
    {
        $this->doctrine = $doctrine;
        $this->cache = $cache;
        $this->entityManager = $entityManager;
    }

    #[Route('/jokes', name: 'app_jokes')]
    #[IsGranted("ROLE_ADMIN")]
    public function ListJokes(): Response
    {
        $jokes = $this->cache->get(CacheKeyDict::JOKES_KEY, function () {
            return $this->doctrine->getRepository(Joke::class)->findAll();
        });

        $categories = $this->cache->get(CacheKeyDict::CATEGORIES_KEY, function () {
            return $this->doctrine->getRepository(Categories::class)->findAll();
        });

        $listjokes = $this->doctrine->getRepository(Joke::class)->findAll();
        if ($listjokes !== $jokes) {
            $this->cache->delete(CacheKeyDict::JOKES_KEY);
        }

        return $this->render('admin/jokeslist.html.twig', [
            'loadJoke' => $jokes,
            'loadCategory' => $categories,
        ]);
    }

    #[Route('/jokes/{id}/update', name: 'app_jokes_update')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function UpdateJoke(Joke $joke, Request $request)
    {
        $form = $this->createForm(AdminJokeType::class, $joke);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($joke);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_jokes', [
                'id' => $joke->getId()
            ]);
        }
        return $this->render('admin/updatejoke.html.twig', [
            'loadJoke' => $form->createView()
        ]);
    }

    #[Route('/jokes/{id}/delete', name: 'app_jokes_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function DeleteJoke(Joke $joke)
    {
        $this->entityManager->remove($joke);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_jokes', [
            'id' => $joke->getId()
        ]);
    }
}
