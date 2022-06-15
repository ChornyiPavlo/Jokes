<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StartPageController extends AbstractController
{
    #[Route('/', name: 'app_page', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('StartPage/index.html.twig', [
            'controller_name' => 'StartPageController',
        ]);
    }
}
