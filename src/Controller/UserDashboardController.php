<?php

namespace App\Controller;

use App\Entity\AiResult;
use App\MyHelpers\PaginationHelper;
use App\Repository\AiResultRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/dashboard', name: 'app_user_dashboard')]
class UserDashboardController extends AbstractController
{

    #[Route('/', name: '_index')]
    public function index(AiResultRepository $aiResultRepository, ProductRepository $productRepository, Request $request): Response
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

            $underverif = $page == 'unverified';

            $template = $this->render('user_dashboard/sub_onsale_products.html.twig', [
                'products' => $map[$page]->getNProducts(10),
                'underverif' => $underverif,
                'type' => $page,
                'aiResult' => $map[$page]->getAiResult(10),
            ]);

            return new JsonResponse([
                'template' => $template->getContent(),
                'currentPage' => $map[$page]->getCurrentPage(),
                'previousPage' => $map[$page]->getPreviousPage(),
                'nbrpages' => $map[$page]->getNbrPages()
            ]);

        }


        $on_sale = $productRepository->findBy(['isDeleted' => false, 'state' => 'verified']);
        $unverified = $productRepository->findBy(['isDeleted' => false, 'state' => 'unverified']);
        $aiResults = $aiResultRepository->findAll();

        $map = [
            'on_sale' => new PaginationHelper($on_sale, 1, 2, ceil(sizeof($on_sale) / 10)),
            'unverified' => new PaginationHelper($unverified, 1, 2, ceil(sizeof($unverified) / 10), $aiResults)
        ];

        $session->set('user_products_map', $map);


        return $this->render('user_dashboard/author.html.twig', [
            'on_sale' => $map['on_sale'],
            'unverified' => $map['unverified']
        ]);

//        return $this->render('user_dashboard/author.html.twig', [
//            'products' => array_slice($prods, 0, 10),
//            'nbr_pages' => ceil(sizeof($prods) / 10),
//            'current_page' => 1,
//            'previous_page' => 2,
//        ]);
    }
}
