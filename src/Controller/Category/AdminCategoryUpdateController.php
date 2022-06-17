<?php

namespace App\Controller\Category;

use App\Entity\Categories;
use App\Form\AdminCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminCategoryUpdateController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/categories/{id}/update', name: 'app_categories_update')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function updateCategory(Categories $category, Request $request)
    {
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_categories', [
                'id' => $category->getId()
            ]);
        }
        return $this->render('admin/updatecategory.html.twig', [
            'loadCategory' => $form->createView()
        ]);
    }
}