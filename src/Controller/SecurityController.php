<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{
    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si aucun rôle spécifique n'est attribué, redirigez vers une page par défaut
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

       
    #[Route(path: '/logout', name: 'app_logout')]
   
    public function logout(): RedirectResponse
    {
        // La méthode sera interceptée par le firewall, et cette redirection ne sera pas exécutée directement
        return new RedirectResponse($this->generateUrl('app_login'));
    }
}
