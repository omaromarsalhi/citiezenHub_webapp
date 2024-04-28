<?php

namespace App\MyHelpers;


use App\Controller\BasketController;
use App\Controller\MailerController;
use App\Repository\AiResultRepository;
use App\Repository\ContractRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SendPdfMessageHandler
{

    public function __construct(private ContractRepository $contractRepository)
    {
    }

    public function __invoke(SendPdfMessage $message): void
    {
        $obj = $message->getObj();

        $basketController = new BasketController();

        $pdfFilePath = $basketController->generatePdf($this->contractRepository,$obj['idSeller'],$obj['idBuyer']);

        $mail = new MailerController();

        $mail->sendMail($pdfFilePath,$obj['emailSeller']);
        $mail->sendMail($pdfFilePath,$obj['emailBuyer']);
    }
}