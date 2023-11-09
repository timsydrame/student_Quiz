<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(QuizRepository $quizRepository, Security $security): Response
    {
        // Vérifiez les rôles et redirigez en conséquence
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_quiz');
        } elseif ($security->isGranted('ROLE_TEACHER')) {
            return $this->redirectToRoute('app_quiz');
        }

        $quizList = $quizRepository->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'quizList'       => $quizList,
        ]);
    }
}
