<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(QuizRepository $quizRepository): Response
    {
        $quizList = $quizRepository->findAll();
        return $this->render('quiz/index.html.twig', [
            'controller_name' => 'QuizController',
            'quizList'       => $quizList,
        ]);
    }

    #[Route('/quiz/new', name: 'app_quiz_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // creates a quiz object
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);

        // gerer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // recuperer les données saisies
            $quiz = $form->getData();

            // Enregistrer dans la table quiz de la BD
            // tell Doctrine you want to (eventually) save the quiz (no queries yet)
            $entityManager->persist($quiz);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            // Rediriger vers la page de liste des quizs
            return $this->redirectToRoute('app_quiz');
        }
        return $this->render('quiz/new.html.twig', [
            'form' => $form->createView(),
        ]);
    
    }

    #[Route('/quiz/edit/{id}', name: 'app_quiz_edit')]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        // recuperer le quiz dont l'id est passé en parametre
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $id
            );
        }
        // Ici le quiz existe et pré-rempli par le repository,
        // on le charge donc dans le formulaire
        $form = $this->createForm(QuizType::class, $quiz);

        // gerer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, mets à jour le quiz
            $entityManager->flush();

            // Redirige vers le tableau de bord (ou une autre route appropriée)
            return $this->redirectToRoute('app_quiz');
        }

        return $this->render('quiz/edit.html.twig', [
            'form' => $form->createView(),
            'quiz' => $quiz, // Tu peux transmettre le quiz à la vue pour afficher les données existantes
        ]);
    }

    // Supression
    #[Route('/quiz/delete/{id}', name: 'app_quiz_delete')]
    public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        // recuperer le quiz dont l'id est passé en parametre
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $id
            );
        }

        // supprimer 
        $entityManager->remove($quiz);
        $entityManager->flush();
        return $this->redirectToRoute('app_quiz'); // Remplace 'app_quiz' par le nom de la route vers la page de tableau de bord


    }
}
