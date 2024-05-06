<?php

namespace App\MyHelpers;
use GuzzleHttp\Client;

class ImageExpliciteApi
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoiN2IzNjdkMjItMDg2NS00MDBiLThiMDYtYTA0NWFmODMyZTQyIiwidHlwZSI6ImFwaV90b2tlbiJ9.Vn0fTezmrfzxyCYZrpjRmsjpAhpCD4pqc8YOTqFJafM'
            ]
        ]);
    }

    public function checkExplicitContent($imageUrl)
    {

        $response = $this->client->post('https://api.edenai.run/v2/image/explicit_content', [
            'json' => [
                'response_as_dict' => true,
                'attributes_as_list' => false,
                'show_original_response' => false,
                'providers' => 'microsoft',
                'file_url' => $imageUrl
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}


