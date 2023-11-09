<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route("/auth")]
class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route("/registration", name: "app_registration")]
    public function index(Request $request, EntityManagerInterface $em)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));

            // Set their role
            $user->setRoles(['ROLE_STUDENT']);

            try {
                // Save
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                // Gérez l'erreur d'unicité de manière appropriée, par exemple :
                $this->addFlash('error', 'Cette adresse e-mail est déjà associée à un compte.  
                Veuillez saisir une autre adresse e-mail pour vous inscrire.');

                // Redirigez ici pour éviter la soumission multiple du formulaire
                return $this->redirectToRoute('app_registration');
            }
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
