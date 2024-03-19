<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductImagesRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/market/place')]
class MarketPlaceController extends AbstractController
{
    #[Route('/', name: 'app_market_place_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository,Request $request): Response
    {
        $session = $request->getSession();

        if($request->isXmlHttpRequest()) {

            $movement_direction=$request->get("movement_direction");

            $prods=$session->get('allProducts');
            $nbr_pages=$session->get('nbr_pages');
            $current_page=$session->get('current_page');
            $previous_page=$current_page;

            if($current_page!=$nbr_pages&&$movement_direction=="next")
                $current_page++;
            else if($current_page!=1&&$movement_direction=="previous")
                $current_page--;
            else
                $current_page=$movement_direction;

            $session->set('current_page',$current_page);



            return  $this->render('market_place/sub_market.html.twig', [
                'products' => array_slice($prods,($current_page-1)*12,12),
                'current_page' => $current_page,
                'previous_page' => $previous_page,
            ]);

        }



        $session->set('allProducts',$productRepository->findAll());
        $prods=$session->get('allProducts');
        $session->set('nbr_pages',ceil(sizeof($prods)/12));
        $session->set('current_page',1);


        return $this->render('market_place/market.html.twig', [
            'products' => array_slice($prods,0,12),
            'nbr_pages'=> ceil(sizeof($prods)/12),
            'current_page' => 1,
            'previous_page' => 2,
        ]);
    }

    #[Route('/new', name: 'app_market_place_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
//        $product = new Product();
//        $form = $this->createForm(ProductType::class, $product);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($product);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
//        }

        return $this->renderForm('market_place/create.html.twig', [
//            'product' => $product,
//            'form' => $form,
        ]);
    }

    #[Route('/{idProd}', name: 'app_market_place_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('market_place/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{idProd}/edit', name: 'app_market_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_place/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{idProd}', name: 'app_market_place_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getIdProd(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
    }
}
