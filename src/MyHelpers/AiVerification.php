<?php

namespace App\MyHelpers;

use Symfony\Component\HttpClient\HttpClient;


class AiVerification
{

    public function run($obj): array
    {
        $result=$this->getAllDesc($obj['images']);
//        $result=$this->compareDescWithTitleAndCategory($result,$obj['title']);
//        var_dump($result);
        return $this->compareDescWithTitleAndCategory($result,$obj['title']);
    }

    public function compareDescWithTitleAndCategory($descriptions,$title): array
    {
        $result=[];
        for($i=0;$i<sizeof($descriptions);$i++){
            $result[]=$this->getAiValidation($descriptions[$i],$title);
        }
        return $result;
    }


    private function getAiValidation($desc,$title): string
    {
        $client = HttpClient::create();
        $response = $client->request('POST','http://127.0.0.1:5000/get-desc_validation?desc='.$desc.'&title='.$title);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return $content;
    }

    public function getAllDesc($images_url): array
    {
        $result=[];
        for($i=0;$i<sizeof($images_url);$i++){
            $result[]=$this->generateImageDescription($images_url[$i]);
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

}