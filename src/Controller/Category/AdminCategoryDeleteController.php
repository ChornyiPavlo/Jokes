<?php

namespace App\Controller\Category;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminCategoryDeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/$categories/{id}/delete', name: 'app_category_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function deleteCategory(Categories $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_categories', [
            'id' => $category->getId()
        ]);
    }
}