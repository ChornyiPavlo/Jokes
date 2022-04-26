<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Form\CategoriesType;
use App\Form\JokeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JokeController extends AbstractController
{
    #[Route('/add joke', name: 'app_joke', methods: ['GET', 'POST'])]
    public function submit(Request $request, ManagerRegistry $doctrine): Response
    {
        $joke = new Joke();
        $form = $this->createForm(JokeType::class, $joke);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //  отправить на модерацию админу?
            // Save
            $entityManager = $doctrine->getManager();
            $entityManager->persist($joke);
            $entityManager->flush();

            return $this->redirectToRoute('app_page');
        }
        return $this->render('joke/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
