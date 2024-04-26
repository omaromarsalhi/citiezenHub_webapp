<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/save-audio', name: 'save_audio', methods: ['POST'])]
class SaveAudioController extends AbstractController {

    public function __construct(
        private readonly ParameterBagInterface $params,
    ) {}

    public function __invoke(Request $request) {
        $audioFile = $request->files->get('audioFile');
        // Check if a file has been uploadedd
        if ($audioFile) {
            $newFilename = uniqid() . '.wav';

            try {
                $audioFile->move('assets/audio', $newFilename);
                $basePath = sprintf(
                    '%s%s',
                    $this->params->get('kernel.project_dir'),
                    '/public/assets/audio',
                );
        
                $client = new Client();
                
                $response = $client->request(
                    Request::METHOD_POST,
                    sprintf('%s/extract-voice', 'http://127.0.0.1:4000'),
                    [
                        'multipart' => [
                            [
                                'name' => 'voice',
                                'contents' => file_get_contents(
                                    sprintf(
                                        '%s/%s',
                                        $basePath,
                                        $newFilename,
                                    )
                                ),
                                'filename' => $newFilename,
                            ],
                        ],
                    ]
                );
    

                $response = json_decode($response->getBody()->getContents(), true);
                return new JsonResponse($response, Response::HTTP_OK);
            } catch (FileException $e) {
                return new JsonResponse('Failed to upload file: ' . $e->getMessage());
            }    
        }

        return new JsonResponse('No file provided', Response::HTTP_BAD_REQUEST);
    }
}