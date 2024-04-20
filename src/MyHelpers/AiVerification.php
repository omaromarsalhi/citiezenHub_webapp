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
        return $this->compareDescWithTitleAndCategory($result,$obj);
    }



    public function getAllDesc($images_url): array
    {
        $result=[];
        for($i=0;$i<sizeof($images_url);$i++){
            $result[]=$this->generateImageDescription($images_url[$i]);
        }
        return $result;
    }

    public function compareDescWithTitleAndCategory($descriptions,$obj): array
    {
        $result=[];
        for($i=0;$i<sizeof($descriptions);$i++){
            $result[][]=$this->getTitleValidation($descriptions[$i],$obj['title']);
            $result[][]=$this->getCategoryValidation($descriptions[$i],$obj['category']);
        }
        return $result;
    }

    private function getTitleValidation($desc,$title): string
    {
        return $this->Http('get-title_validation?desc='.$desc.'&title='.$title);
    }

    private function getCategoryValidation($desc,$category): string
    {
        return $this->Http('get-category_validation?desc='.$desc.'&category='.$category);
    }

    private function generateImageDescription($image_url): string
    {
        $absolute_path='C:\Users\omar salhi\Desktop\PIDEV\citiezenHub_webapp\public\usersImg\\'.$image_url;
        return $this->Http('get-product_image_descreption?image_url='.$absolute_path);
    }


    private function Http($url): string
    {
        $client = HttpClient::create();
        $response = $client->request('POST','http://127.0.0.1:5000/'.$url);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return $content;
    }


}