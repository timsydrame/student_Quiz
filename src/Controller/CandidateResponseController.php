<?php

namespace App\Controller;

use App\Entity\CandidateResponse;
use App\Entity\Question;
use App\Form\CandidateResponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidateResponseController extends AbstractController
{
    #[Route('/candidateResponse', name: 'app_candidate_response')]
    public function index(): Response
    {
        return $this->render('candidate_response/index.html.twig', [
            'controller_name' => 'CandidateResponseController',
        ]);
    }

    #[Route('/question/{questionId}/candidateResponse/new', name: 'app_candidate_response_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, $questionId): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($questionId);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $candidateResponse = new CandidateResponse();
        $candidateResponse->setQuestion($question);

        $form = $this->createForm(CandidateResponseType::class, $candidateResponse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $candidateResponse = $form->getData();
            $entityManager->persist($candidateResponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_edit', ['questionId' => $questionId]);
        }

        return $this->render('candidateResponse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/question/{questionId}/candidateResponse/{responseId}/edit', name: 'app_candidate_response_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $questionId, $responseId): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($questionId);
        $candidateResponse = $entityManager->getRepository(CandidateResponse::class)->find($responseId);

        if (!$question || !$candidateResponse) {
            throw $this->createNotFoundException('Question ou réponse non trouvée');
        }

        if ($candidateResponse->getQuestion() !== $question) {
            throw $this->createNotFoundException('La réponse ne correspond pas à la question');
        }

        $form = $this->createForm(CandidateResponseType::class, $candidateResponse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_edit', ['questionId' => $questionId]);
        }

        return $this->render('candidateResponse/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

    #[Route('/candidateResponse/delete/{candidateResponseId}', name: 'app_candidate_response_delete')]
    public function delete(EntityManagerInterface $entityManager, $candidateResponseId): Response
    {
        $candidateResponseRepository = $entityManager->getRepository(CandidateResponse::class);
        $candidateResponse = $candidateResponseRepository->find($candidateResponseId);

        if (!$candidateResponse) {
            throw $this->createNotFoundException('Réponse non trouvée');
        }

        $questionId = $candidateResponse->getQuestion()->getId();

        $entityManager->remove($candidateResponse);
        $entityManager->flush();

        return $this->redirectToRoute('app_question_edit', ['questionId' => $questionId]);
    }
}
