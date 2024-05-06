<?php

namespace App\MyHelpers;
use GuzzleHttp\Client;

class OpenCageService
{
    private $apiKey;
    private $client;

    public function __construct()
    {
        $this->apiKey = $_ENV['OPENCAGE_API_KEY']; // or getenv('OPENCAGE_API_KEY')
        $this->client = new Client([
            'base_uri' => 'https://api.opencagedata.com/geocode/v1/',
        ]);
    }

    public function geocode(string $query)
    {
        $response = $this->client->request('GET', 'json', [
            'query' => [
                'q' => $query,
                'key' => $this->apiKey,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
