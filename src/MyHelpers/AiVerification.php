<?php

namespace App\MyHelpers;

use Symfony\Component\HttpClient\HttpClient;


class AiVerification
{
    private $images_url;

    public function __construct($images_url)
    {
        $this->images_url=$images_url;
    }

    public function run(): array
    {
        $result=[];
        for($i=0;$i<sizeof($this->images_url);$i++){
            $result[]=$this->generateImageDescription($this->images_url[$i]);
        }
        return $result;
    }



    private function generateImageDescription($image_url): string
    {
        $client = HttpClient::create();
        $absolute_path='C:\Users\omar salhi\Desktop\PIDEV\citiezenHub_webapp\public\usersImg\\'.$image_url;
        $response = $client->request('POST','http://127.0.0.1:5000/get-product_image_descreption?image_url='.$absolute_path);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return $content;
    }

    private function HttpRequest($url): string
    {
        $client = HttpClient::create();
        $response = $client->request('POST', 'http://127.0.0.1:5000/get-product_image_descreption?image_url='.$url);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return $content;
    }
}