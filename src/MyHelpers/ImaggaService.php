<?php

namespace App\MyHelpers;

use GuzzleHttp\Client;

class ImaggaService
{
    private $client;
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->apiKey =  $_ENV['IMAGGA_API_KEY'];;
        $this->apiSecret = $_ENV['IMAGGA_API_SECRET'];;
        $this->client = new Client([
            'base_uri' => 'https://api.imagga.com/v2/',
        ]);
    }

    public function tagImage($imagePath)
    {
        // Read the image file content
        $imageContent = file_get_contents($imagePath);

        // Encode the image content as base64
        $base64Image = base64_encode($imageContent);

        // Send the image content in the request
        $response = $this->client->request('POST', 'tags', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret),
            ],
            'form_params' => [
                'image_base64' => $base64Image,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}