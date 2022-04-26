<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Entity\User;
use App\Form\AdminCategoryType;
use App\Form\AdminJokeType;
use App\Form\AdminUserType;
use App\Form\CategoriesType;
use App\Form\JokeType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine)
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users', name: 'app_users')]
    public function listUsers(): Response
    {
        $loadUsers = $this->doctrine->getRepository(User::class)->findAll();

           return $this->render('admin/users.html.twig', [
            'loadUsers' => $loadUsers,
        ]);
    }

    #[Route('/users/{id}/edit', name: 'app_users_edit')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Done');

            return $this->redirectToRoute('app_users',[
                'id'=>$user->getId()
                ]);
        }
        return $this->render('admin/edituser.html.twig', [
            'loadUser' => $form->createView()
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_users_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function delete(User $user, Request $request, EntityManagerInterface $entityManager)
    {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Done');

            return $this->redirectToRoute('app_users',[
                'id'=>$user->getId()
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function managerCategories(Request $request): Response
    {
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/categories.html.twig', [
            'loadCategory' => $loadCategory,
        ]);
    }

    #[Route('/categories/add', name: 'app_categories_add')]
    public function register(Request $request): Response
    {
        $category = new Categories();
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories');
        }
        return $this->render('admin/addcategory.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'app_categories_edit')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function editCat(Categories $category, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AdminCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Done');

            return $this->redirectToRoute('app_categories',[
                'id'=>$category->getId()
            ]);
        }
        return $this->render('admin/editcategory.html.twig', [
            'loadCategory' => $form->createView()
        ]);
    }

    #[Route('/$categories/{id}/delete', name: 'app_category_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function deleteCat(Categories $category, Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_categories',[
            'id'=>$category->getId()
        ]);
    }

    #[Route('/jokes', name: 'app_jokes')]
    public function managerJokes(Request $request, EntityManagerInterface $entityManager): Response
    {
        $loadJoke = $this->doctrine->getRepository(Joke::class)->findAll();
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/jokes.html.twig', [
            'loadJoke' => $loadJoke,
            'loadCategory' => $loadCategory,
        ]);
    }

    #[Route('/jokes/{id}/edit', name: 'app_jokes_edit')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function editJoke(Joke $joke, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AdminJokeType::class, $joke);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($joke);
            $entityManager->flush();
            $this->addFlash('success', 'Done');

            return $this->redirectToRoute('app_jokes',[
                'id'=>$joke->getId()
            ]);
        }
        return $this->render('admin/editjoke.html.twig', [
            'loadJoke' => $form->createView()
        ]);
    }

    #[Route('/jokes/{id}/delete', name: 'app_jokes_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function deleteJoke(Joke $joke, Request $request, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($joke);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_jokes',[
            'id'=>$joke->getId()
        ]);
    }

}
