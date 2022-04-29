<?php

namespace App\Controller;

use App\Entity\JokeModeration;
use App\Form\ModerJokeType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JokeController extends AbstractController
{
    #[Route('/add joke', name: 'app_joke')]
    public function submit(Request $request, ManagerRegistry $doctrine): Response
    {
        $joke = new JokeModeration();
        $form = $this->createForm(ModerJokeType::class, $joke);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $joke ->setUser($this->getUser()->getEmail());
            $joke->setCreated(date_create('now'));

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
