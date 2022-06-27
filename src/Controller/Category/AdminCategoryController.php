<?php

namespace App\Controller\Category;

use App\Entity\Categories;
use App\Form\AdminCategoryType;
use App\Event\SampleMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminCategoryController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private MessageBusInterface $bus;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry        $doctrine,
        MessageBusInterface    $bus,
        EntityManagerInterface $entityManager,
    )
    {
        $this->doctrine = $doctrine;
        $this->bus = $bus;
        $this->entityManager = $entityManager;
    }

    #[Route('/categories/create', name: 'app_categories_create')]
    #[IsGranted("ROLE_ADMIN")]
    public function CreateCategory(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->bus->dispatch(new SampleMessage($category->getName() . ' created'));

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('admin/createcategory.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    #[IsGranted("ROLE_ADMIN")]
    public function ReadCategories(): Response
    {
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'loadCategory' => $loadCategory,
        ]);
    }

    #[Route('/categories/{id}/update', name: 'app_categories_update')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function UpdateCategory(Categories $category, Request $request)
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

    #[Route('/$categories/{id}/delete', name: 'app_category_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function DeleteCategory(Categories $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_categories', [
            'id' => $category->getId()
        ]);
    }
}