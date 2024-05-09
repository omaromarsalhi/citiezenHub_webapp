<?php

namespace App\EventListener;

use App\Controller\ProductSSEController;
use App\CustomEvent\ProductCustomEvent;
use App\Entity\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class ProductEventListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'product.updated' => 'onProductUpdated',
            'product.added' => 'onProductAdded',
        ];
    }

    public function onProductUpdated(ProductCustomEvent $event): void
    {
        echo "omaaaaaaaaaaaaaat";
    }

    public function onProductAdded(ProductCustomEvent $event): void
    {

    }

    private function fetchProductData(Product $product): array
    {
        return [
            'id' => $product->getIdProduct(),
            'name' => $product->getName(),
        ];
    }
}
