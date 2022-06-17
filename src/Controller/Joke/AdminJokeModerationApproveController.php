<?php

namespace App\Controller\Joke;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Entity\JokeModeration;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminJokeModerationApproveController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine,
    )
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/moderate/{id}/approve', name: 'app_jokes_approve')]
    #[IsGranted("ROLE_ADMIN")]
    public function approveJoke(Request $request)
    {
        $idJoke = $request->attributes->get('id');
        /** @var JokeModeration $jokeModeration */
        $jokeModeration = $this->doctrine->getRepository(JokeModeration::class)->find($idJoke);
        if (null === $jokeModeration) {
            throw new NotFoundHttpException();
        }

        $joke = new Joke();
        $joke->setCategoryId($jokeModeration->getCategoryId());
        $joke->setJoke($jokeModeration->getJoke());

        $this->doctrine->getManager()->persist($joke);
        $this->doctrine->getManager()->remove($jokeModeration);

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke->getId()
        ]);
    }
}
