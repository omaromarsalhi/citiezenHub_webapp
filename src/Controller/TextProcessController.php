<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/process-text', name: 'process_text', methods: ['POST'])]
class TextProcessController extends AbstractController
{
    private $client;

    public function __construct(ParameterBagInterface $params)
    {
        $this->client = new Client([
            'base_uri' => 'http://127.0.0.1:4000/',
        ]);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        try {
            $response = $this->client->request('POST', 'extract-voice', [
                'json' => ['text' => $text]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            return new JsonResponse($body, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to process text: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
