<?php

namespace App\Controller;

use App\MyHelpers\PaginationHelper;
use App\Repository\AiResultRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/dashboard', name: 'app_user_dashboard')]
class UserDashboardController extends AbstractController
{

    #[Route('/', name: '_index')]
    public function index(TransactionRepository $transactionRepository, AiResultRepository $aiResultRepository, ProductRepository $productRepository, Request $request): Response
    {
        $session = $request->getSession();

        if ($request->isXmlHttpRequest()) {

            $movement_direction = $request->get("movement_direction");
            $page = $request->get("page");

            $map = $session->get('user_products_map');

            $current_page = $map[$page]->getCurrentPage();
            $previous_page = $current_page;

            if ($current_page != $map[$page]->getNbrPages() && $movement_direction == "next")
                $current_page++;
            else if ($current_page != 1 && $movement_direction == "previous")
                $current_page--;
            else if ($movement_direction != "next" && $movement_direction != "previous" && $movement_direction > 0)
                $current_page = $movement_direction;

            $map[$page]->setCurrentPage($current_page);
            $map[$page]->setPreviousPage($previous_page);
            $session->set('user_products_map', $map);


            if (($page == 'on_sale' || $page == 'unverified')) {
                $underverif = $page == 'unverified';
                $template = $this->render('user_dashboard/sub_onsale_products.html.twig', [
                    'products' => $map[$page]->getNProducts(10),
                    'underverif' => $underverif,
                    'type' => $page,
                    'aiResult' => $map[$page]->getAiResult(10),
                ]);
            } else {
                $template = $this->render('user_dashboard/sub_saled_purchased.html.twig', [
                    'transactions' => $map[$page]->getNProducts(10),
                    'type' => $page,
                ]);
            }

            return new JsonResponse([
                'template' => $template->getContent(),
                'currentPage' => $map[$page]->getCurrentPage(),
                'previousPage' => $map[$page]->getPreviousPage(),
                'nbrpages' => $map[$page]->getNbrPages()
            ]);
        }

        $on_sale = $productRepository->findBy(['user' => $this->getUser(), 'state' => 'verified']);
        $unverified = $productRepository->findBy(['user' => $this->getUser(), 'state' => 'unverified']);
        $aiResults = $aiResultRepository->findByIdProduct($productRepository->findByIdUser($this->getUser()));

        $purchased = $transactionRepository->findBy(['idBuyer' => $this->getUser()]);
        $sold = $transactionRepository->findBy(['idSeller' => $this->getUser()]);


        $map = [
            'on_sale' => new PaginationHelper($on_sale, 1, 2, ceil(sizeof($on_sale) / 10)),
            'unverified' => new PaginationHelper($unverified, 1, 2, ceil(sizeof($unverified) / 10), $aiResults),
            'purchased' => new PaginationHelper($purchased, 1, 2, ceil(sizeof($purchased) / 10)),
            'sold' => new PaginationHelper($sold, 1, 2, ceil(sizeof($sold) / 10)),
        ];

        $session->set('user_products_map', $map);

        return $this->render('user_dashboard/author.html.twig', [
            'on_sale' => $map['on_sale'],
            'unverified' => $map['unverified'],
            'purchased' => $map['purchased'],
            'sold' => $map['sold'],
        ]);

    }
}
