<?php

namespace App\MyHelpers;


use App\Controller\BasketController;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendPdfMessageHandler
{

    public function __invoke(SendPdfMessage $message): void
    {

        $basketController = new BasketController();

        $basketController->generatePdf($message->getObj());

    }
}