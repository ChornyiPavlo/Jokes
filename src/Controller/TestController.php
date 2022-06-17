<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    #[IsGranted("ROLE_ADMIN")]
    public function tindex(): Response
    {
        return $this->json(['hyi' => 123, 'pizda'=> 'blabla']);
    }

    #[Route('/best', name: 'app_best')]
    #[IsGranted("ROLE_ADMIN")]
    public function bindex(): Response
    {
        $arr = ['hyi' => 1234, 'pizda'=> 'blablabla'];

        return new Response(json_encode($arr));
    }
}