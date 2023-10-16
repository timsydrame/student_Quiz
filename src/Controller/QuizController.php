<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizController extends AbstractController
{
    #[Route('/api/quiz', name: 'api_quiz_list', methods: ['GET'])]
    public function apiList(QuizRepository $quizRepository): JsonResponse
    {
        $quizList = $quizRepository->findAll();
        $data = [];

        foreach ($quizList as $quiz) {
            $data[] = [
                'id' => $quiz->getId(),
                'name' => $quiz->getName(), // Ajoutez d'autres champs que vous souhaitez inclure
                // ...
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/quiz/{id}', name: 'api_quiz_show', methods: ['GET'])]
    public function apiShow($id, QuizRepository $quizRepository): JsonResponse
    {
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            return $this->json(['message' => 'Quiz not found'], 404);
        }

        // Récupérer les questions associées au quiz
        $questions = $quiz->getQuestions();

        $data = [
            'id' => $quiz->getId(),
            'name' => $quiz->getName(),
            'questions' => [],  // Initialise un tableau pour stocker les questions
        ];

        // Boucler sur les questions pour les ajouter à la réponse
        foreach ($questions as $question) {
            $questionData = [
                'id' => $question->getId(),
                'text' => $question->getEnonce(),
                'responses' => [],  // Initialise un tableau pour stocker les réponses de la question
            ];

            // Récupérer les réponses associées à la question
            $responses = $question->getCandidateResponses();

            // Boucler sur les réponses pour les ajouter à la question
            foreach ($responses as $response) {
                $responseData = [
                    'id' => $response->getId(),
                    'text' => $response->getEnoncer(),
                    // Autres données de réponse que vous souhaitez inclure
                ];

                $questionData['responses'][] = $responseData;
            }

            $data['questions'][] = $questionData;
        }

        return $this->json($data);
    }



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
