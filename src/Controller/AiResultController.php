<?php

namespace App\Controller;

use App\Entity\AiResult;
use App\Form\AiResult2Type;
use App\MyHelpers\AiDataHolder;
use App\Repository\AiResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/ai/result', name: 'app_ai_result_')]
class AiResultController extends AbstractController
{
//    #[Route('/', name: 'app_ai_result_index', methods: ['GET'])]
//    public function index(AiResultRepository $aiResultRepository): Response
//    {
//        return $this->render('ai_result/index.html.twig', [
//            'ai_results' => $aiResultRepository->findAll(),
//        ]);
//    }

    #[Route('/check4verification', name: 'check', methods: ['GET', 'POST'])]
    public function index(AiResultRepository $aiResultRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
//            $aiDataHolder->setDescriptions([" The image shows a single, whole red apple. It's positioned against a plain white background,
//                allowing the focus to be solely on the apple. The apple appears to be of high quality with a shiny skin
//                and a clear reflection on its surface, suggesting it might be fresh. There are no other objects or text in the image."]);
//            $aiDataHolder->setTitleValidation([" No, the paragraph does not mention Omar Salhi."]);
//            $aiDataHolder->setTitleValidation([" Yes, the main object of the paragraph is a red apple, which falls under the category of food;"]);
            $doesItExist=false;
            $aiResult = $aiResultRepository->findOneBy(['idProduct' => $request->get('idProduct')]);

            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $aiDataHolder = $serializer->deserialize($aiResult->getBody(), AiDataHolder::class, 'json');


            $result = [];
            $sub_result = [];
            for($i=0;$i<sizeof($aiDataHolder->getDescriptions());$i++) {
                $sub_result['title']=str_starts_with(strtolower($aiDataHolder->getTitleValidation()[$i]), " yes");
                $sub_result['titleData']=$aiDataHolder->getTitleValidation()[$i];
                $sub_result['category']=str_starts_with(strtolower($aiDataHolder->getCategoryValidation()[$i]), " yes");
                $sub_result['categoryData']=$aiDataHolder->getCategoryValidation()[$i];
                $result[]=$sub_result;
                $doesItExist=true;
            }
//            var_dump($result);
//            return new JsonResponse(['doesItExist'=>false], Response::HTTP_OK);
            return new JsonResponse(['doesItExist'=>$doesItExist,'data' => $result], Response::HTTP_OK);
        }
        return new Response('bad request', Response::HTTP_BAD_REQUEST);
    }




    public function new(AiResult $aiResult, EntityManagerInterface $entityManager): void
    {
        $entityManager->persist($aiResult);
        $entityManager->flush();
    }

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
        $form = $this->createForm(AiResult2Type::class, $aiResult);
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
