<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\MyHelpers\ImageHelper;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


#[Route('/product', name: 'app_product')]
class ProductController extends AbstractController
{
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ImageHelper $imageHelper, ProductRepository $productRepository): Response
    {

        if ($request->isXmlHttpRequest()) {

            $name = $request->get("name");
            $description = $request->get("description");
            $price = $request->get("price");
            $quantity = $request->get("quantity");
            $category = $request->get("category");

            $new_product = new Product();

            $new_product->setIdUser(1);
            $new_product->setName($name);
            $new_product->setDescreption($description);
            $new_product->setPrice(floatval($price));
            $new_product->setQuantity(floatval($quantity));
            $new_product->setCategory($category);
            $new_product->setIsDeleted(0);
            $new_product->setState('unverified');
            $new_product->setType('BIEN');

            $entityManager->persist($new_product);
            $entityManager->flush();

            $product = $productRepository->findOneBy([], ['idProduct' => 'DESC']);

            $images = $request->files->all();
            for ($i = 0; $i < sizeof($images); $i++) {
                $imageHelper->saveImages($images['file-' . ($i + 1)], $product);
            }

            return new JsonResponse(['state' => 'done']);
        }


        return $this->render('market_place/create.html.twig');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/generate_description', name: '_show', methods: ['GET', 'POST'])]
    public function generate_description(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
        $title = $request->get("title");
        $client = HttpClient::create();
        $response = $client->request('POST', 'http://127.0.0.1:5000/get-descreption?title=' . $title);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return new JsonResponse(['description' => $content]);
        }
        return null;
    }



//    #[Route('/{idProd}', name: '_show', methods: ['GET'])]
//    public function show(Product $product): Response
//    {
//
//        return $this->render('market_place/show.html.twig', [
//            'product' => $product,
//        ]);
//    }

    #[Route('/{idProd}/edit', name: '_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{idProd}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getIdProd(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
    }
}
