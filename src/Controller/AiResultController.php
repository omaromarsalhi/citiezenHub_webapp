<?php

namespace App\Controller;

use App\Entity\AiResult;
use App\Form\AiResult1Type;
use App\Repository\AiResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[Route('/ai/result')]
class AiResultController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_ai_result_index', methods: ['GET'])]
    public function index(AiResultRepository $aiResultRepository): Response
    {
        return $this->render('ai_result/index.html.twig', [
            'ai_results' => $aiResultRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'new')]
    public function new($aiResult): Response
    {
        $this->entityManager->persist($aiResult);
        $this->entityManager->flush();
        return new Response('done', Response::HTTP_CREATED);
    }


//    #[Route('/new', name: 'app_ai_result_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        dump($request);
//        $entityManager->persist($request->get('json'));
//        $entityManager->flush();
//        return new Response('done', Response::HTTP_CREATED);
//    }

    #[Route('/{idAiResult}', name: 'app_ai_result_show', methods: ['GET'])]
    public function show(AiResult $aiResult): Response
    {
        return $this->render('ai_result/show.html.twig', [
            'ai_result' => $aiResult,
        ]);
    }

    #[Route('/{idAiResult}/edit', name: 'app_ai_result_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AiResult $aiResult, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AiResult1Type::class, $aiResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ai_result_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ai_result/edit.html.twig', [
            'ai_result' => $aiResult,
            'form' => $form,
        ]);
    }

    #[Route('/{idAiResult}', name: 'app_ai_result_delete', methods: ['POST'])]
    public function delete(Request $request, AiResult $aiResult, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $aiResult->getIdAiResult(), $request->request->get('_token'))) {
            $entityManager->remove($aiResult);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ai_result_index', [], Response::HTTP_SEE_OTHER);
    }
}
