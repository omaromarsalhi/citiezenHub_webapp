<?php

namespace App\CustomEvent;



use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductCustomEvent extends Event
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
