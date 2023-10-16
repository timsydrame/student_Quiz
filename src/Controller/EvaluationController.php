<?php

namespace App\Controller;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation/{id}', name: 'app_evaluation')]
    public function index(Request $request, $id, EntityManagerInterface $entityManager): Response 
    {
        // recuperer le quiz dont l'id est passé en parametre
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $id
            );
        }
        
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
                'enonce' => $question->getEnonce(),
                'responses' => [],  // Initialise un tableau pour stocker les réponses de la question
            ];

            // Récupérer les réponses associées à la question
            $responses = $question->getCandidateResponses();

            // Boucler sur les réponses pour les ajouter à la question
            foreach ($responses as $response) {
                $responseData = [
                    'id' => $response->getId(),
                    'enonce' => $response->getEnoncer(),
                    // Autres données de réponse que vous souhaitez inclure
                ];

                $questionData['responses'][] = $responseData;
            }

            $data['questions'][] = $questionData;
        }
        return $this->render('evaluation/index.html.twig', [
            // 'form' => $form->createView(),
            'data' => $data // Tu peux transmettre le quiz à la vue pour afficher les données existantes
        ]);
    }

    #[Route('/evaluation/submit/{id}', name: 'app_evaluation_submit')]
    public function evaluate(Request $request, $id, EntityManagerInterface $entityManager): Response 
    {
        // recuperer le quiz dont l'id est passé en parametre
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $id
            );
        }

        $questions = $quiz->getQuestions();
        $totalQuestions = count($questions);
        $correctAnswers = 0;

        // Récupérer les réponses soumises
        $submittedResponses = $request->request->get('responses');
        // dump($request);
        
        // Boucler sur les questions pour les ajouter à la réponse
        foreach ($questions as $question) {
            $questionId = $question->getId();
            $submittedQuestion = $submittedResponses[$questionId] ?? [];
            // Récupérer les réponses associées à la question
            $responses = $question->getCandidateResponses();

            // Boucler sur les réponses pour les ajouter à la question
            foreach ($responses as $response) {
                $responseId = $response->getId();
                $submittedResponse = $submittedQuestion[$responseId] ?? [];

                if($response->isIscorrect()) {
                    // Vérifier si la réponse soumise est correcte
                    $isCorrect = in_array($responseId, $submittedQuestion);

                    // Faire quelque chose avec cette information
                    if ($isCorrect) {
                        // La réponse est correcte
                        $correctAnswers++;
                    }
                }

            }
            
        }
        // Calculer le pourcentage de bonnes réponses
        $percentageCorrect = ($correctAnswers / $totalQuestions) * 100;
        dump($percentageCorrect);
        dump($submittedResponses);
        return $this->redirectToRoute('app_evaluation',
            ['id' => $id]
        );
    }
    
}
