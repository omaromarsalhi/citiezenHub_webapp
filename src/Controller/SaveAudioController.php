<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/save-audio', name: 'save_audio', methods: ['POST'])]
class SaveAudioController extends AbstractController {

    public function __construct() {}

    public function __invoke(Request $request) {
        $audioFile = $request->files->get('audioFile');
        if ($audioFile) {
            $newFilename = uniqid() . '.wav';

            try {
                $audioFile->move('assets/audio', $newFilename);
                return new Response('File is uploaded successfully');
            } catch (FileException $e) {
                return new Response('Failed to upload file: ' . $e->getMessage());
            }
        }

        return new Response('No file provided', Response::HTTP_BAD_REQUEST);
    }
}