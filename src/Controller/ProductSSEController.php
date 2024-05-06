<?php

namespace App\Controller;

use App\CustomEvent\ProductCustomEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductSSEController extends AbstractController
{

    private static $product;

    public static function setData(ProductCustomEvent $event): void
    {
        self::$product = $event->getProduct();
    }


    #[Route('/sse/product', name: 'productSSE')]
    public function index(): StreamedResponse
    {
        $response = new StreamedResponse(function () {
            while (true) {
                // Fetch the most recent product data from your database
                $productData = $this->formatProductData();
                // Send the product data as an SSE event
                echo "data: " . json_encode($productData) . "\n\n";
                ob_flush();
                flush();
                sleep(5); // Adjust the interval as needed
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    private function formatProductData(): array
    {
        if (self::$product == null) {
            return [
                'id' => 'none',
                'name' => 'none',
            ];
        } else {
            return [
                'id' => self::$product->getIdProduct(),
                'name' => self::$product->getName(),
            ];
        }
    }
}

