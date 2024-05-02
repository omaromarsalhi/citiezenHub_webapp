<?php

namespace App\MyHelpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class UploadImage
{
    function uploadImageToImgBB($imageFile)
    {
        $client = new Client();
        try {
            $response = $client->request('POST', 'https://api.imgbb.com/1/upload', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($imageFile->getRealPath(), 'r'),
                        'filename' => $imageFile->getClientOriginalName()
                    ]
                ],
                'query' => [
                    'key' => '1c9db8e911f710aefd280ae2cef2d695',
                ]
            ]);

            $body = $response->getBody();
            $result = json_decode($body->getContents(), true);

            if ($response->getStatusCode() == 200) {
                return $result['data']['url'];
            } else {
                throw new \Exception('Image upload failed: ' . $result['error']['message']);
            }
        } catch (RequestException $e) {
            throw new \Exception('Image upload request failed: ' . $e->getMessage());
        }
    }
}
