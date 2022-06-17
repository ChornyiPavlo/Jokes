<?php


namespace App\Controller\Joke;

use App\Entity\Joke;
use App\Form\AdminJokeType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route ('/admin')]
class AdminJokeUpdateController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/jokes/{id}/update', name: 'app_jokes_update')]
    #[IsGranted("ROLE_ADMIN", subject: "joke")]
    public function updateJoke(Joke $joke, Request $request)
    {
        $form = $this->createForm(AdminJokeType::class, $joke);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($joke);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_jokes', [
                'id' => $joke->getId()
            ]);
        }
        return $this->render('admin/updatejoke.html.twig', [
            'loadJoke' => $form->createView()
        ]);
    }

}
