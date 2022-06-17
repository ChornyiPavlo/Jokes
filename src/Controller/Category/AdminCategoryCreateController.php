<?php

namespace App\Controller\Category;

use App\Entity\Categories;
use App\Form\AdminCategoryType;
use App\messenger\SampleMessage;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminCategoryCreateController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private MessageBusInterface $bus;

    public function __construct(
        ManagerRegistry $doctrine,
        MessageBusInterface $bus,
    )
    {
        $this->doctrine = $doctrine;
        $this->bus = $bus;
    }

    #[Route('/categories/create', name: 'app_categories_create')]
    #[IsGranted("ROLE_ADMIN")]
    public function createCategory(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
// send message to queue
            $message = new SampleMessage('Created new category');
            $this->bus->dispatch($message,[new DelayStamp(3000)]);

            return $this->redirectToRoute('app_categories');


        }
        return $this->render('admin/createcategory.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }
}