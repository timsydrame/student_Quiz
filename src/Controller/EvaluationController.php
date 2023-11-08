<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\EvaluationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation/{id}', name: 'app_evaluation')]
    public function index(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Aucun quiz ne correspond à l\'ID ' . $id);
        }

        $questions = $quiz->getQuestions();

        $form = $this->createForm(EvaluationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les réponses soumises depuis le formulaire
            $submittedResponses = $form->get('responses')->getData();

            // Initialisez le score
            $score = 0;

            // Parcourez les questions et comparez les réponses soumises avec les réponses correctes
            foreach ($questions as $question) {
                $candidateResponses = $question->getCandidateResponses();

                // Comparez les réponses soumises avec les réponses candidates
                $correct = true;
                foreach ($candidateResponses as $candidateResponse) {
                    if (!in_array($candidateResponse->getId(), $submittedResponses)) {
                        $correct = false;
                        break;
                    }
                }

                if ($correct) {
                    // Ajoutez un point au score si la réponse est correcte
                    $score++;
                }
            }

            // Redirigez l'utilisateur vers une page de résultats avec le score
            return $this->redirectToRoute('app_evaluation_submit', ['id' => $id, 'score' => $score]);
        }

        return $this->render('evaluation/index.html.twig', [
            'form' => $form->createView(),
            'questions' => $questions,
            'quiz' => $quiz,
        ]);
    }

    #[Route('/evaluation/submit/{id}', name: 'app_evaluation_submit')]
    public function submit(Request $request, $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);

        if (!$quiz) {
            throw $this->createNotFoundException('Aucun quiz ne correspond à l\'ID ' . $id);
        }

        $questions = $quiz->getQuestions();
        $totalQuestions = count($questions);
        $totalCorrectQuestions = 0; // Total des questions correctement répondues

        // Initialisez un tableau pour stocker les données des questions
        $questionData = [];

        // Récupérez l'ID du quiz actuel
        $quizId = $quiz->getId();

        if ($request->getMethod() === 'POST') {
            $session->set('quiz_scores', []);
        }

        foreach ($questions as $question) {
            $correctResponses = $question->getCandidateResponses()->filter(function ($response) {
                return $response->isIscorrect();
            });

            $correctResponseIds = $correctResponses->map(function ($response) {
                return $response->getId();
            })->toArray();

            $submittedResponseIds = $request->request->get('form')['responses'] ?? [];

            if (is_array($submittedResponseIds)) {
                $submittedResponseIds = array_map('intval', $submittedResponseIds);
            } else {
                $submittedResponseIds = [];
            }

            // Vérifiez si toutes les réponses correctes de la question sont sélectionnées par l'utilisateur
            $correctlyAnswered = empty(array_diff($correctResponseIds, $submittedResponseIds));

            if ($correctlyAnswered) {
                $totalCorrectQuestions++;
            }

            // Ajoutez les informations de la question aux données
            $questionData[] = [
                'question' => $question->getEnonce(),
                'correctResponses' => $correctResponseIds,
                'userResponses' => $submittedResponseIds,
            ];
        }

        $percentageCorrect = ($totalCorrectQuestions / $totalQuestions) * 100;

        // Récupérez les scores stockés dans la session
        $quizScores = $session->get('quiz_scores', []);
        // Stockez le score dans la session en utilisant l'ID du quiz actuel comme clé
        $quizScores[$quizId] = $percentageCorrect;
        $session->set('quiz_scores', $quizScores);

        return $this->render('evaluation/results.html.twig', [
            'quiz' => $quiz,
            'percentageCorrect' => $percentageCorrect,
            'totalQuestions' => $totalQuestions,
            'questionData' => $questionData,
            'quizScore' => $quizScores[$quizId], // Récupérez le score du quiz actuel
        ]);
    }


}
