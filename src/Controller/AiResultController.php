<?php

namespace App\Controller;

use App\Entity\AiResult;
use App\Form\AiResult2Type;
use App\MyHelpers\AiDataHolder;
use App\MyHelpers\AiVerificationMessage;
use App\Repository\AiResultRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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
            $aiResult = $aiResultRepository->findOneBy(['idProduct' => $request->get('idProduct')]);
            if ($aiResult != null) {
                $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
                $aiDataHolder = $serializer->deserialize($aiResult->getBody(), AiDataHolder::class, 'json');

                $result = [];
                $sub_result = [];
                for ($i = 0; $i < sizeof($aiDataHolder->getDescriptions()); $i++) {
                    $sub_result['title'] = str_starts_with(strtolower($aiDataHolder->getTitleValidation()[$i]), " yes");
                    $sub_result['titleData'] = $aiDataHolder->getTitleValidation()[$i];
                    $sub_result['category'] = str_starts_with(strtolower($aiDataHolder->getCategoryValidation()[$i]), " yes");
                    $sub_result['categoryData'] = $aiDataHolder->getCategoryValidation()[$i];
                    $result[] = $sub_result;
                }
                return new JsonResponse(['doesItExist' => true, 'data' => $result], Response::HTTP_OK);
            }
            return new JsonResponse(['doesItExist' => false], Response::HTTP_OK);
        }
        return new Response('bad request', Response::HTTP_BAD_REQUEST);
    }


    #[Route('/reverify', name: 'reverify', methods: ['GET', 'POST'])]
    public function reverify(MessageBusInterface $messageBus, ProductRepository $productRepository, AiResultRepository $aiResultRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {
            $aiResult = $aiResultRepository->findOneBy(['idProduct' => $request->get('idProduct')]);
            $this->delete($aiResult, $entityManager);

            $product = $productRepository->findOneBy(['idProduct' => $request->get('idProduct')]);

            $paths = [];
            for ($i = 0; $i < sizeof($product->getImages()); $i++) {
                $paths[] = str_replace('usersImg/', '', $product->getImages()[$i]->getPath());
            }

            $obj = [
                'title' => $product->getName(),
                'category' => $product->getCategory(),
                'id' => $product->getIdProduct(),
                'images' => $paths,
            ];

            $messageBus->dispatch(new AiVerificationMessage($obj));

            return new JsonResponse(['resp' => 'done'], Response::HTTP_OK);
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

    public static function delete(AiResult $aiResult, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($aiResult);
        $entityManager->flush();
        return new Response('done', Response::HTTP_OK);
    }
}
