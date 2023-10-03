<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\PossibleResponse;
use App\Form\PossibleResponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PossibleResponseController extends AbstractController
{
    #[Route('/possibleResponse', name: 'app_possible_response')]
    public function index(): Response
    {
        return $this->render('possible_response/index.html.twig', [
            'controller_name' => 'PossibleResponseController',
        ]);
    }

    #[Route('/question/{questionId}/possibleResponse/new', name: 'app_possible_response_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, $questionId): Response
    {
        // Find the quiz by ID
        $question = $entityManager->getRepository(Question::class)->find($questionId);

        if (!$question) {
            // Gérer le cas où le quiz n'est pas trouvé (par exemple, rediriger vers une page d'erreur)
            throw $this->createNotFoundException('reponse non trouvé non trouvé');
        }

        // Creates a question object and associates it with the quiz
        $possibleResponse = new PossibleResponse();
        $possibleResponse->setQuestion($question);

        $form = $this->createForm(PossibleResponseType::class, $possibleResponse);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données saisies
            $possibleResponse = $form->getData();

            // Enregistrer dans la table question de la BD
            $entityManager->persist($possibleResponse);

            // Effectuer les requêtes (par exemple, la requête INSERT)
            $entityManager->flush();

            // Rediriger vers la page de liste des questions pour ce quiz (ou toute autre action appropriée)
            return $this->redirectToRoute('app_question_edit',
             ['questionId' => $questionId]);

        }

        return $this->render('possibleResponse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/question/{questionId}/possibleResponse/{responseId}/edit', name: 'app_possible_response_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $questionId, $responseId): Response
    {
        // Find the question and possible response by ID
        $question = $entityManager->getRepository(Question::class)->find($questionId);
        $possibleResponse = $entityManager->getRepository(PossibleResponse::class)->find($responseId);

        if (!$question || !$possibleResponse) {
            // Gérer le cas où la question ou la réponse n'est pas trouvée
            throw $this->createNotFoundException('Question ou réponse non trouvée');
        }

        // Vérifiez que la réponse appartient à la question
        if ($possibleResponse->getQuestion() !== $question) {
            throw $this->createNotFoundException('La réponse ne correspond pas à la question');
        }

        // Créez le formulaire de modification de la réponse
        $form = $this->createForm(PossibleResponseType::class, $possibleResponse);

        // Gérez la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            // Redirigez vers la page de modification de la question
            return $this->redirectToRoute('app_question_edit', ['questionId' => $questionId]);
        }

        return $this->render('possibleResponse/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

    #[Route('/possibleResponse/delete/{possibleResponseId}', name: 'app_possible_response_delete')]
    public function delete(EntityManagerInterface $entityManager, $possibleResponseId): Response
    {
        $possibleResponseRepository = $entityManager->getRepository(PossibleResponse::class);
        $possibleResponse = $possibleResponseRepository->find($possibleResponseId);

        if (!$possibleResponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        // Récupérer le questionId avant de supprimer la réponse
        $questionId = $possibleResponse->getQuestion()->getId();

        $entityManager->remove($possibleResponse);
        $entityManager->flush();

        // Rediriger vers une page appropriée (peut-être la liste des réponses ou une autre page)
        return $this->redirectToRoute('app_question_edit', ['questionId' => $questionId]);
    }

}
