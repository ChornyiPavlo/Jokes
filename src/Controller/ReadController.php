<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Form\CategoriesType;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReadController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/read/alljokes', name: 'app_read_all', methods: ['GET', 'POST'])]
    public function readall(Request $request): Response
    {
        $allcategory = $this->doctrine->getRepository(Categories::class)->findAll();
        $alljokes = $this->doctrine->getRepository(Joke::class)->findAll();

        return $this->render('read/alljokes.html.twig', [
            'alljokes'=>$alljokes,
            'allcategory'=>$allcategory,
        ]);
    }

    #[Route('/read', name: 'app_read', methods: ['GET', 'POST'])]
    public function choose(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jokes = $this->doctrine->getRepository(Joke::class)->findby(['category_id'=>$category->getId()]);
            return $this->render('read/jokes.html.twig', [
                'jokes'=>$jokes,
                'category_id'=>$category->getName()
            ]);
        }
        return $this->render('read/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//    #[Route('/read/alljokes/{category_id}', name: 'app_read_cat', methods: ['GET', 'POST'])]
//    public function read(Request $request, ManagerRegistry $doctrine): Response
//    {
//
//        $category = new Categories();
//        $form = $this->createForm(CategoriesType::class, $category);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $jokes = $this->doctrine->getRepository(Joke::class)->findby(['category_id'=>$category->getId()]);
//            return $this->render('read/jokes.html.twig', [
//                'jokes'=>$jokes,
//                'category_id'=>$category->getName()
//            ]);
//        }
//        return $this->render('read/alljokes.html.twig', [
//            'form' => $form->createView(),
//        ]);
//
//    }

}

