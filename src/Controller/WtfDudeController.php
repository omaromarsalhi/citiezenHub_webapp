<?php

namespace App\Controller;

use App\MyHelpers\AiVerification;
use App\Repository\ProductRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

class WtfDudeController extends AbstractController
{
    #[Route('/wtf/dude', name: 'app_wtf_dude')]
    public function index(ProductRepository $productRepository): Response
    {

        $filter = [
        'datetime'=>['today'=> 'false', 'lastWeek'=> 'false', 'lastMonth'=> 'false'],
        'category'=>['food'=> 'true', 'sports'=> 'false', 'entertainment'=> 'false', 'realEstate'=> 'false', 'vehicle'=> 'false'],
        'price'=>['allPrices'=> 'true', 'asc'=> 'false', 'desc'=> 'false']
        ];
        
        dump($productRepository->findByPriceTest($filter));

        die();
        return new Response("done");
    }
}
