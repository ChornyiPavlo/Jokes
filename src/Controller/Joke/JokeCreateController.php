<?php

namespace App\Controller\Joke;

use App\Entity\JokeModeration;
use App\Form\ModerJokeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JokeCreateController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine,
    )
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/create joke', name: 'app_joke_create')]
    public function submit(Request $request): Response
    {
        $joke = new JokeModeration();
        $form = $this->createForm(ModerJokeType::class, $joke);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $joke->setUser($this->getUser()->getEmail());
            $joke->setCreated(date_create('now'));

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($joke);
            $entityManager->flush();

            return $this->redirectToRoute('app_page');
        }
        return $this->render('joke/createjoke.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
