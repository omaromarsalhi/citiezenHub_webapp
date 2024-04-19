<?php

namespace App\MyHelpers;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AiListener implements EventSubscriberInterface
{
    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();
        var_dump($product);
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductCreatedEvent::class => 'onProductCreated',
        ];
    }
}