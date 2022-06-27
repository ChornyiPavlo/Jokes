<?php

namespace App\Controller\Joke;

use App\CacheKeyDict;
use App\Entity\Categories;
use App\Entity\Joke;
use App\Entity\JokeModeration;
use App\Form\ModerJokeType;
use App\Repository\JokeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class JokeController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private PaginatorInterface $paginator;
    private CacheInterface $cache;

    public function __construct(
        ManagerRegistry    $doctrine,
        PaginatorInterface $paginator,
        CacheInterface     $cache,
    )
    {
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->cache = $cache;
    }

    #[Route('/create joke', name: 'app_joke_create')]
    public function CreateJoke(Request $request): Response
    {
        $joke = new JokeModeration();
        $form = $this->createForm(ModerJokeType::class, $joke);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $joke->setUser($this->getUser()->getEmail());
            $joke->setCreated(new \DateTime());

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($joke);
            $entityManager->flush();

            return $this->redirectToRoute('app_page');
        }
        return $this->render('joke/createjoke.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/select category', name: 'app_category_select')]
    public function SelectCategoryJoke(): Response
    {
        $categories = $this->doctrine->getRepository(Categories::class)->findAll();
        return $this->render('joke/read/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/select category/{id}', name: 'app_read_jokes_category')]
    public function ReadCategoryJoke(Request $request): Response
    {
        $categoryId = $request->attributes->get('id');
        $categoryJokes = $this->doctrine->getRepository(Joke::class)->findby(['category' => $categoryId]);
        $categoryName = $this->doctrine->getRepository(Categories::class)->findBy(['id' => $categoryId]);

        return $this->render('joke/read/jokes.html.twig', [
            'jokes' => $categoryJokes,
            'category' => $categoryName
        ]);
    }

    #[Route('/read/alljokes', name: 'app_read_all')]
    public function ReadAllJokes(JokeRepository $joke, Request $request): Response
    {
        $alljokes = $this->cache->get(CacheKeyDict::JOKES_KEY, function () {
            return $this->doctrine->getRepository(Joke::class)->findAll();
        });

        $q = $request->query->get('q');
        $queryBuilder = $joke->getWithSearchQueryBuilder($q);
        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt('StartPage', 1)/*StartPage number*/,
            10/*limit per StartPage*/
        );

        return $this->render('joke/read/alljokes.html.twig', [
            'alljokes' => $alljokes,
            'pagination' => $pagination,
        ]);
    }


}

