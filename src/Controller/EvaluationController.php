<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\EvaluationQuizType;
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

        $data = $this->prepareEvaluationData($quiz);

        $form = $this->createForm(EvaluationQuizType::class, $data);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données saisies
            $data = $form->getData();
            return $this->forward('App\Controller\EvaluationController::result', [
                'data' => $data,
            ]);
        }

        return $this->render('evaluation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/evaluation-result', name: 'app_evaluation_result', methods: 'POST' )]
    public function result(Request $request, EntityManagerInterface $entityManager): Response 
    {
        // Get the submitted data from the request
        $requestData = $request->request->all();
        $quizId = $requestData['quiz_id'];
        $questions = $requestData['questions'];
        
        $evaluationData['questions'] = $questions;
        $evaluationData['quizId'] = $quizId;

        $quizDataWithResult = $this->computeEvaluationResultData($evaluationData, $entityManager);
        
        dump($quizDataWithResult);

        return $this->render('evaluation/result.html.twig', [
            'data' => $quizDataWithResult,
        ]);
    }

    private function computeEvaluationResultData($evaluationData, EntityManagerInterface $entityManager): array {
        
        $quiz = $this->getQuizById($evaluationData['quizId'], $entityManager);

        $data = [
            'id' => $quiz->getId(),
            'name' => $quiz->getName(),
            'description' => $quiz->getDescription(),
            'questions' => [],  // Initialise un tableau pour stocker les questions
        ];

        $questions = $quiz->getQuestions();

        // Boucler sur les questions
        $totalQuestions = 0;
        $correctlyAnsweredQuestions = 0; // Total des questions correctement répondues
        foreach ($questions as $question) {

            // Récupérer les réponses associées à la question
            // Nous considérons uniquement les questions ayant au moins 2 repéonses
            $responses = $question->getCandidateResponses();
            if ($responses->count() < 2) {
                continue;
            }

            // On comptabilise la question (car ayant au moins 2 réponses candidates)
            $totalQuestions++;

            $quizQuestionId = $question->getId();
            $questionData = [
                'id' => $quizQuestionId,
                'enonce' => $question->getEnonce(),
                'responses' => [],  // Initialise un tableau pour stocker les réponses de la question
            ];

            // Récupérer les 'id' des réponses sélectionnées par l'utilisateur pour cette question
            $selectedResponseIds = $this->getUserResponseIds($evaluationData, $quizQuestionId);

            $isQuizQuestionCorrectlyAnswered = true;
            // Boucler sur les réponses pour les ajouter à la question
            foreach ($responses as $response) {
                $quizQuestionResponseId = $response->getId();
                $isCorrect = $response->isIscorrect();

                $isChecked = in_array($quizQuestionResponseId, $selectedResponseIds);

                $isResponseCorrectlyAnswered = ($isCorrect == $isChecked);

                $responseData = [
                    'id' => $quizQuestionResponseId,
                    'enonce' => $response->getEnoncer(),
                    'isCorrect' => $isCorrect,
                    'isChecked' => $isChecked,
                    'isResponseCorrectlyAnswered' =>  $isResponseCorrectlyAnswered,
                    // Autres données de réponse que vous souhaitez inclure
                ];
                
                // Dès qu'une des réponses n'est pas correcte,
                // alors la question est ratée dans sa globalité 
                if (!$isResponseCorrectlyAnswered) {
                    $isQuizQuestionCorrectlyAnswered = false;
                }

                $questionData['responses'][] = $responseData;
            }

            // Après avoir parcourru toutes les réponses, 
            // on comptabilise 1 point pr la question si correctement répondu
            if ($isQuizQuestionCorrectlyAnswered) {
                $correctlyAnsweredQuestions++;
            }
            // On indique si la question a été correctement répondue
            $questionData['isQuestionCorrectlyAnswered'] = $isQuizQuestionCorrectlyAnswered;
            $data['questions'][] = $questionData;
        }

        $percentageCorrect = ($correctlyAnsweredQuestions / $totalQuestions) * 100;

        $data['percentageCorrect'] = $percentageCorrect;
        $data['totalQuestions'] = $totalQuestions;
        $data['totalCorrectlyAnsweredQuestions'] = $correctlyAnsweredQuestions;

        return $data;
    }

    private function getUserResponseIds($evaluationData, $quizQuestionId): array {
        $selectedResponses = array_filter($evaluationData['questions'][$quizQuestionId]['responses'], function ($response) {
            return array_key_exists('isChecked', $response);
        });

        $selectedResponseIds = array_map(function ($selectedResponse) { return intval($selectedResponse['id']); }, $selectedResponses);

        return $selectedResponseIds;
    }

    private function prepareEvaluationData(Quiz $quiz): array {
        $data = [
            'id' => $quiz->getId(),
            'name' => $quiz->getName(),
            'description' => $quiz->getDescription(),
            'questions' => [],  // Initialise un tableau pour stocker les questions
        ];

        $questions = $quiz->getQuestions();

        // Boucler sur les questions pour les ajouter à la réponse
        foreach ($questions as $question) {

            // Récupérer les réponses associées à la question
            // Nous considérons uniquement les questions ayant au moins 2 repéonses
            $responses = $question->getCandidateResponses();
            if ($responses->count() < 2) {
                continue;
            }

            $questionData = [
                'id' => $question->getId(),
                'enonce' => $question->getEnonce(),
                'responses' => [],  // Initialisation d'un tableau pour stocker les réponses de la question
            ];

            // Boucler sur les réponses pour les ajouter à la question
            foreach ($responses as $response) {
                $responseData = [
                    'id' => $response->getId(),
                    'enonce' => $response->getEnoncer(),
                    'isChecked' => false,                   
                ];

                $questionData['responses'][] = $responseData;
            }

            $data['questions'][] = $questionData;
        }

        return $data;
    }

    private function getQuizById($id, EntityManagerInterface $entityManager): Quiz {
        $quizRepository = $entityManager->getRepository(Quiz::class);
        $quiz = $quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $id
            );
        }
        return $quiz;
    }
    
}
