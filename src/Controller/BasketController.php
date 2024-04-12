<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/basket', name: 'app_basket')]
class BasketController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(BasketRepository $basketRepository,ProductRepository $productRepository): Response
    {
        $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);
        $products=array();
        foreach ($basket_items as $basket) {
            $products[]=$productRepository->findBy(['idProduct' => $basket->getProduct()->getIdProduct()]);
//            $products[]= $basket->getProduct();
        }
//        dump($products);
//        die();
        return $this->render('market_place/basket.html.twig',[
            'products'=>$products,
            'basket_items'=>$basket_items
        ]);
    }

    #[Route('/new', name: '_add')]
    public function new(BasketRepository $basketRepository,ProductRepository $productRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()){
            $product =$productRepository->findOneBy(['idProduct'=>$request->get("id")]) ;

            $new_basket = new Basket();
            $user=$this->getUser();

            $new_basket->setProduct($product);
            $new_basket->setUser($user);
            $new_basket->setQuantity(1);

            $entityManager->persist($new_basket);
            $entityManager->flush();


            return new Response(sizeof($basketRepository->findBy(['user'=>$user])), Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }

    #[Route('/remove', name: '_remove')]
    public function remove(BasketRepository $basketRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()){
            $basket_id =$request->get("id") ;

            $entityManager->remove($basketRepository->findOneBy(['idBasket'=>$basket_id]));
            $entityManager->flush();

            return new Response(sizeof($basketRepository->findBy(['user'=>$this->getUser()])), Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }
}
