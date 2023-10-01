<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    #[Route('/question', name: 'app_question')]
    public function index(): Response
    {
        return $this->render('question/index.html.twig', [
            'controller_name' => 'QuestionController',
        ]);
    }

    #[Route('/quiz/{quizId}/question/new', name: 'app_question_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, $quizId): Response
    {
        // Find the quiz by ID
        $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);

        if (!$quiz) {
            // Gérer le cas où le quiz n'est pas trouvé (par exemple, rediriger vers une page d'erreur)
            throw $this->createNotFoundException('Quiz non trouvé');
        }

        // Creates a question object and associates it with the quiz
        $question = new Question();
        $question->setQuiz($quiz);

        $form = $this->createForm(QuestionType::class, $question);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données saisies
            $question = $form->getData();

            // Enregistrer dans la table question de la BD
            $entityManager->persist($question);

            // Effectuer les requêtes (par exemple, la requête INSERT)
            $entityManager->flush();

            // Rediriger vers la page de liste des questions pour ce quiz (ou toute autre action appropriée)
            return $this->redirectToRoute('app_quiz_edit', 
            ['id' => $quiz->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/question/edit/{questionId}', name: 'app_question_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $questionId): Response
    {
        // recuperer le quiz dont l'id est passé en parametre
        $questionRepository = $entityManager->getRepository(Question::class);
        $question = $questionRepository->find($questionId);
        if (!$question) {
            throw $this->createNotFoundException(
                'Aucun quiz ne correspond à l\'id  ' . $questionId
            );
        }
        // Ici le quiz existe et pré-rempli par le repository,
        // on le charge donc dans le formulaire
        $form = $this->createForm(QuestionType::class, $question);

        // gerer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, mets à jour le quiz
            $entityManager->flush();

            // Redirige vers le tableau de bord (ou une autre route appropriée)
            return $this->redirectToRoute('app_quiz_edit', ['id' => $question->getQuiz()->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question, // Tu peux transmettre le quiz à la vue pour afficher les données existantes
        ]);
    }
}
