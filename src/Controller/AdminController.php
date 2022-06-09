<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Joke;
use App\Entity\JokeModeration;
use App\Entity\User;
use App\Form\AdminCategoryType;
use App\Form\AdminJokeType;
use App\Form\AdminUserType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route ('/admin')]
class AdminController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $doctrine;
    private PaginatorInterface $paginator;
    private CacheInterface $cache;

    public function __construct(UserPasswordHasherInterface $passwordHasher,
                                ManagerRegistry             $doctrine,
                                PaginatorInterface          $paginator,
                                CacheInterface              $cache
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
        $this->paginator = $paginator;
        $this->cache = $cache;
    }

    #[Route('/', name: 'app_admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users', name: 'app_users')]
    #[IsGranted("ROLE_ADMIN")]
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

            return $this->redirectToRoute('app_users', [
                'id' => $user->getId()
            ]);
        }
        return $this->render('admin/edituser.html.twig', [
            'loadUser' => $form->createView()
        ]);
    }

    #[Route('/users/{id}/delete', name: 'app_users_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "user")]
    public function delete(User $user, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_users', [
            'id' => $user->getId()
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    #[IsGranted("ROLE_ADMIN")]
    public function managerCategories(Request $request, CategoriesRepository $repository): Response
    {
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        $q = $request->query->get('c');
        $queryBuilder = $repository->getWithSearchQueryBuilder($q);
        $pagination = $this->paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            3/*limit per page*/
        );
        return $this->render('admin/categories.html.twig', [
            'loadCategory' => $loadCategory,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/categories/add', name: 'app_categories_add')]
    #[IsGranted("ROLE_ADMIN")]
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

            return $this->redirectToRoute('app_categories', [
                'id' => $category->getId()
            ]);
        }
        return $this->render('admin/editcategory.html.twig', [
            'loadCategory' => $form->createView()
        ]);
    }

    #[Route('/$categories/{id}/delete', name: 'app_category_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "category")]
    public function deleteCat(Categories $category, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_categories', [
            'id' => $category->getId()
        ]);
    }

    #[Route('/jokes', name: 'app_jokes')]
    #[IsGranted("ROLE_ADMIN")]
    public function managerJokes(): Response
    {
        $cacheloadJoke = $this->doctrine->getRepository(Joke::class)->findAll();
        $cacheloadCategory = $this->doctrine->getRepository(Categories::class)->findAll();
        $loadJoke = $this->cache->get('k', function () use ($cacheloadJoke) {
            return $cacheloadJoke;
        });
        $loadCategory = $this->cache->get('c', function () use ($cacheloadCategory) {
            return $cacheloadCategory;
        });

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

            return $this->redirectToRoute('app_jokes', [
                'id' => $joke->getId()
            ]);
        }
        return $this->render('admin/editjoke.html.twig', [
            'loadJoke' => $form->createView()
        ]);
    }

    #[Route('/jokes/{id}/delete', name: 'app_jokes_delete')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function deleteJoke(Joke $joke, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($joke);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_jokes', [
            'id' => $joke->getId()
        ]);
    }

    #[Route('/moderate', name: 'app_jokes_moderate')]
    #[IsGranted("ROLE_ADMIN")]
    public function ModerateJokes(): Response
    {
        $loadJoke = $this->doctrine->getRepository(JokeModeration::class)->findAll();
        $loadCategory = $this->doctrine->getRepository(Categories::class)->findAll();

        return $this->render('admin/moderatejokes.html.twig', [
            'loadJoke' => $loadJoke,
            'loadCategory' => $loadCategory,
        ]);
    }

    #[Route('/moderate/{id}/approve', name: 'app_jokes_approve')]
    #[IsGranted("ROLE_ADMIN")]
    public function approveJoke(EntityManagerInterface $entityManager, JokeModeration $jokeModeration)
    {
        $cat = $this->doctrine->getRepository(Categories::class)->findBy(['id' => $jokeModeration->getCategory()]);
        foreach ($cat as $key => $item) {
            $item;
        }
        $joke = new Joke();
        $joke->setCategory($item);
        $joke->setJoke($jokeModeration->getJoke());
        $entityManager->persist($joke);
        $entityManager->remove($jokeModeration);

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke->getId()
        ]);
    }

    #[Route('/moderate/{id}/decline', name: 'app_jokes_decline')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function declineJoke(JokeModeration $joke, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($joke);
        $entityManager->flush();
        $this->addFlash('success', 'Done');

        return $this->redirectToRoute('app_jokes_moderate', [
            'id' => $joke->getId()
        ]);
    }

}
