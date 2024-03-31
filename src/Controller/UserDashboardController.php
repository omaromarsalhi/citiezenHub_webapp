<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImages;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user/dashboard', name: 'app_user_dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(ProductRepository $productRepository,Request $request): Response
    {

        $session = $request->getSession();

        if ($request->isXmlHttpRequest()) {

            $movement_direction = $request->get("movement_direction");

            $prods = $session->get('allProducts4Owner');
            $nbr_pages = $session->get('nbr_pages4Owner');
            $current_page = $session->get('current_page4Owner');
            $previous_page = $current_page;

            if ($current_page != $nbr_pages && $movement_direction == "next")
                $current_page++;
            else if ($current_page != 1 && $movement_direction == "previous")
                $current_page--;
            else
                $current_page = $movement_direction;

            $session->set('current_page4Owner', $current_page);


            return $this->render('user_dashboard/sub_onsale_products.html.twig', [
                'products' => array_slice($prods, ($current_page - 1) * 10, 10),
                'current_page' => $current_page,
                'previous_page' => $previous_page,
            ]);
        }

        $session->set('allProducts4Owner', $productRepository->findBy(['isDeleted' => false]));
        $prods = $session->get('allProducts4Owner');
        $session->set('nbr_pages4Owner', ceil(sizeof($prods) / 10));
        $session->set('current_page4Owner', 1);


        return $this->render('user_dashboard/author.html.twig', [
            'products' => array_slice($prods, 0, 10),
            'nbr_pages' => ceil(sizeof($prods) / 10),
            'current_page' => 1,
            'previous_page' => 2,
        ]);

    }
}
