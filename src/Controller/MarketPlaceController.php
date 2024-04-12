<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/market/place')]
class MarketPlaceController extends AbstractController
{

    #[Route('/', name: 'app_market_place_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isXmlHttpRequest()) {

            $movement_direction = $request->get("movement_direction");

            $prods = $session->get('allProducts');
            $nbr_pages = $session->get('nbr_pages');
            $current_page = $session->get('current_page');
            $previous_page = $current_page;

            if ($current_page != $nbr_pages && $movement_direction == "next")
                $current_page++;
            else if ($current_page != 1 && $movement_direction == "previous")
                $current_page--;
            else
                $current_page = $movement_direction;

            $session->set('current_page', $current_page);


            return $this->render('market_place/sub_market.html.twig', [
                'products' => array_slice($prods, ($current_page - 1) * 12, 12),
                'current_page' => $current_page,
                'previous_page' => $previous_page,
            ]);

        }

        $session->set('allProducts', $productRepository->findAll());
        $prods = $session->get('allProducts');
        $session->set('nbr_pages', ceil(sizeof($prods) / 12));
        $session->set('current_page', 1);

//        $productimage=new ProductImages();
//        $productimage->setPath('usersImg/f76c774e989a81e8ad43906570a26d48.png');
//
//        $prodss=new Product();
//        $prodss=$prodss->addProductImage($productimage);

//        dump($prods);
//        for($i=0;$i<sizeof($prods);$i++)
//            echo $prods[$i]->getImages()[0]->getPath();
//        die();

        return $this->render('market_place/market.html.twig', [
            'products' => array_slice($prods, 0, 12),
            'nbr_pages' => ceil(sizeof($prods) / 12),
            'current_page' => 1,
            'previous_page' => 2,
        ]);
    }


}
