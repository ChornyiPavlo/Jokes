<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class RegistrationController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private ManagerRegistry $doctrine;
    private UserAuthenticatorInterface $userAuthenticator;
    private FormLoginAuthenticator $formLoginAuthenticator;

    public function __construct(UserPasswordHasherInterface $passwordHasher,
                                ManagerRegistry $doctrine,
                                UserAuthenticatorInterface $userAuthenticator,
                                FormLoginAuthenticator $formLoginAuthenticator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->doctrine = $doctrine;
        $this->userAuthenticator = $userAuthenticator;
        $this->formLoginAuthenticator = $formLoginAuthenticator;
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->userAuthenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);

            return $this->redirectToRoute('app_page');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}



