<?php

namespace App\Service;

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

    public function tagImage($imageUrl)
    {
        $response = $this->client->request('GET', 'tags', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiSecret),
            ],
            'query' => [
                'image_url' => $imageUrl,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
 