<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Form\CategoriesType;
use App\Repository\JokeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ReadController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private PaginatorInterface $paginator;
    private CacheInterface $cache;

    public function __construct(ManagerRegistry $doctrine, PaginatorInterface $paginator, CacheInterface $cache)
    {
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->cache = $cache;
    }

    #[Route('/read/alljokes', name: 'app_read_all', methods: ['GET', 'POST'])]
    public function readall(Request $request, JokeRepository $repository): Response
    {
        $allcategory = $this->doctrine->getRepository(Categories::class)->findAll();
        $cachejokes = $this->doctrine->getRepository(Joke::class)->findAll();
        $alljokes = $this->cache->get('key', function () use ($cachejokes) {
            return $cachejokes;
        });

        $q = $request->query->get('q');
        $queryBuilder = $repository->getWithSearchQueryBuilder($q);
        $pagination = $this->paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('read/alljokes.html.twig', [
            'alljokes' => $alljokes,
            'allcategory' => $allcategory,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/read', name: 'app_read', methods: ['GET', 'POST'])]
    public function choose(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jokes = $this->doctrine->getRepository(Joke::class)->findby(['category_id' => $category->getId()]);
            return $this->render('read/jokes.html.twig', [
                'jokes' => $jokes,
                'category_id' => $category->getId()
            ]);
        }
        return $this->render('read/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
