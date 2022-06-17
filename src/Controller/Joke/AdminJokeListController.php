<?php
namespace App\Controller\Joke;

use App\CacheKeyDict;
use App\Entity\Categories;
use App\Entity\Joke;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route ('/admin')]
class AdminJokeListController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private CacheInterface $cache;

    public function __construct(
        ManagerRegistry $doctrine,
        CacheInterface  $cache
    )
    {
        $this->doctrine = $doctrine;
        $this->cache = $cache;
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
        if ($listjokes !== $jokes){
            $this->cache->delete(CacheKeyDict::JOKES_KEY);
        }

        return $this->render('admin/jokeslist.html.twig', [
            'loadJoke' => $jokes,
            'loadCategory' => $categories,
        ]);
    }
}
