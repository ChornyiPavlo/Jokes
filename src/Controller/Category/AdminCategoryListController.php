<?php

namespace App\Controller\Category;

use App\Entity\Categories;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminCategoryListController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/categories', name: 'app_categories')]
    #[IsGranted("ROLE_ADMIN")]
    public function ListCategories(): Response
    {
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'loadCategory' => $loadCategory,
        ]);
    }
}