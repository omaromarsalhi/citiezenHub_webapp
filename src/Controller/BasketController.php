<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Contract;
use App\Entity\Transaction;
use App\Repository\BasketRepository;
use App\Repository\ContractRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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


    #[Route('/update', name: '_update')]
    public function update(BasketRepository $basketRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()){
            $new_quantities =$request->get("new_quantities") ;
            $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);
            for($i=0;$i<sizeof($basket_items);$i++){
                $basket_items[$i]->setQuantity($new_quantities[$i]);
                $entityManager->flush();
            }

            return new Response('success', Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }


    #[Route('/proceedCheckOut', name: '_proceedCheckOut')]
    public function proceedCheckOut(ContractRepository $contractRepository,BasketRepository $basketRepository, Request $request,EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()){

            $address=$request->get("address");
            /** @var UserInterface $user */
            $user = $this->getUser();
            $userId = $user->getId();

            $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);

            for($i=0;$i<sizeof($basket_items);$i++){
                $new_contract= new Contract();
                $new_contract->setTitle("Contrat of selling ".$basket_items[$i]->getProduct()->getName() );
                $new_contract->setTerminationDate(new \DateTime());
                $new_contract->setPurpose("Buying this Item");
                $new_contract->setTermsAndConditions("title");
                $new_contract->setPaymentMethod("title");
                $new_contract->setRecivingLocation($address);

                $entityManager->persist($new_contract);
                $entityManager->flush();

                $contract = $contractRepository->findOneBy([], ['idContract' => 'DESC']);

                $new_transaction= new Transaction();
                $new_transaction->setProduct($basket_items[$i]->getProduct());
                $new_transaction->setContract($contract);
                $new_transaction->setIdSeller($basket_items[$i]->getProduct()->getIdUser());
                $new_transaction->setIdBuyer($userId);
                $new_transaction->setPricePerUnit($basket_items[$i]->getProduct()->getPrice());
                $new_transaction->setQuantity($basket_items[$i]->getQuantity());
                $new_transaction->setTransactionMode("SELL");

                $entityManager->persist($new_transaction);
                $entityManager->flush();
            }

            return new Response('success', Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }
}
