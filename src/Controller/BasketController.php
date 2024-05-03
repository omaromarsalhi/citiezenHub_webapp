<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Contract;
use App\Entity\Transaction;
use App\MyHelpers\SendPdfMessage;
use App\Repository\BasketRepository;
use App\Repository\ContractRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use TCPDF;

// Import TCPDF class

#[Route('/basket', name: 'app_basket')]
class BasketController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(BasketRepository $basketRepository, ProductRepository $productRepository): Response
    {
        $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);
        $products = array();
        foreach ($basket_items as $basket) {
            $products[] = $productRepository->findBy(['idProduct' => $basket->getProduct()->getIdProduct()]);
        }

        return $this->render('market_place/basket.html.twig', [
            'products' => $products,
            'basket_items' => $basket_items
        ]);
    }

    #[Route('/count', name: '_count')]
    public function count(BasketRepository $basketRepository, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $nbr = $basketRepository->count(['user' => $this->getUser()]);
            return new Response($nbr, Response::HTTP_OK);
        }
        return new Response('', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/new', name: '_add')]
    public function new(BasketRepository $basketRepository, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {

            $product = $productRepository->findOneBy(['idProduct' => $request->get("id")]);

            $new_basket = new Basket();
            $user = $this->getUser();

            $new_basket->setProduct($product);
            $new_basket->setUser($user);
            $new_basket->setQuantity(1);

            $product->setQuantity($product->getQuantity() - 1);

            $entityManager->persist($new_basket);
            $entityManager->flush();

            return new Response($basketRepository->count(['user' => $user]), Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }

    #[Route('/remove', name: '_remove')]
    public function remove(BasketRepository $basketRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {
            $basket_id = $request->get("id");

            $entityManager->remove($basketRepository->findOneBy(['idBasket' => $basket_id]));
            $entityManager->flush();

            return new Response(sizeof($basketRepository->findBy(['user' => $this->getUser()])), Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }


    #[Route('/update', name: '_update')]
    public function update(ProductRepository $productRepository, BasketRepository $basketRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        if ($request->isXmlHttpRequest()) {
            $new_quantities = $request->get("new_quantities");
            $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);
            for ($i = 0; $i < sizeof($basket_items); $i++) {
                $product = $basket_items[$i]->getProduct();
                $product->setQuantity($product->getQuantity() - ($new_quantities[$i] - $basket_items[$i]->getQuantity()));
                $basket_items[$i]->setProduct($product);
                $basket_items[$i]->setQuantity($new_quantities[$i]);
                $entityManager->flush();
            }

            return new Response('success', Response::HTTP_OK);
        }
        return $this->render('market_place/basket.html.twig');
    }


    #[Route('/proceedCheckOut', name: '_proceedCheckOut')]
    public function proceedCheckOut(MessageBusInterface $messageBus, ContractRepository $contractRepository, BasketRepository $basketRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isXmlHttpRequest()) {

            $address = $request->get("address");
            /** @var UserInterface $user */
            $user = $this->getUser();
            $userId = $user->getId();

            $basket_items = $basketRepository->findBy(['user' => $this->getUser()]);
            $new_transaction = new Transaction();
            $new_contract = new Contract();

            for ($i = 0; $i < sizeof($basket_items); $i++) {
                $new_contract->setTitle("Contract of selling " . $basket_items[$i]->getProduct()->getName());
                $new_contract->setTerminationDate(new \DateTime());
                $new_contract->setPurpose("Buying this Item");
                $new_contract->setTermsAndConditions(
                    "1/iurf airfgyu &irfuh airfu azirhf arf_ azr ifarifu\n" .
                    "2/kjhfv aeirugyh aeriguh zeriuuv zaeiurh gvaeur gh ufv\n" .
                    "3/aiyuzr aizuyryfg aoiuyrf \n" .
                    "4/aiyuzr aizuyryfg aoiuyrf rrtyhzyjz ztey rtyh\n" .
                    "5/aiyuzr aizuyryfg  , ftujse qrgztheyik brghert \n");
                $new_contract->setPaymentMethod("Credit Card");
                $new_contract->setRecivingLocation($address);

                $entityManager->persist($new_contract);
                $entityManager->flush();

                $contract = $contractRepository->findOneBy([], ['idContract' => 'DESC']);

                $new_transaction->setProduct($basket_items[$i]->getProduct());
                $new_transaction->setContract($contract);
                $new_transaction->setIdSeller($basket_items[$i]->getProduct()->getUser()->getId());
                $new_transaction->setIdBuyer($userId);
                $new_transaction->setPricePerUnit($basket_items[$i]->getProduct()->getPrice());
                $new_transaction->setQuantity($basket_items[$i]->getQuantity());
                $new_transaction->setTransactionMode("SELL");

                $entityManager->persist($new_transaction);
                $entityManager->flush();

                $html = $this->renderView('contract/pdf_template.html.twig', [
                    'transaction' => $new_transaction
                ]);

                $obj = [
                    'emailSeller' => 'omar.salhi.job@gmail.com',
                    'emailBuyer' => 'omar.salhi.job@gmail.com',
                    'idSeller' => $new_transaction->getIdSeller(),
                    'idBuyer' => $new_transaction->getIdBuyer(),
                    'html' => $html
                ];

                $messageBus->dispatch(new SendPdfMessage($obj));
            }

            $basketRepository->clear($this->getUser());


            return new Response('success', Response::HTTP_OK);
        }
        return $this->render('contract/success.html.twig');
    }

    #[Route('/generatePdfWithoutMail', name: '_generatePdfWithoutMail')]
    public function generatePdfWithoutMail(Request $request, TransactionRepository $transactionRepository): Response
    {
        $transactions = $transactionRepository->findOneBy(['idTransaction' => $request->get('idTransaction')]);

        $html = $this->renderView('contract/pdf_template.html.twig', [
            'transaction' => $transactions
        ]);

        $pdf = new TCPDF();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Omar Salhi');
        $pdf->SetTitle('PDF Transaction');
        $pdf->SetSubject('Transaction Details');
        $pdf->SetKeywords('PDF, Transaction');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('transaction.pdf', 'D');

        return new Response('', Response::HTTP_OK);
    }

    public function generatePdf($obj): void
    {
        $mail = new MailerController();
        $pdf = new TCPDF();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Omar Salhi');
        $pdf->SetTitle('PDF Transaction');
        $pdf->SetSubject('Transaction Details');
        $pdf->SetKeywords('PDF, Transaction');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage();
        $pdf->writeHTML($obj['html'], true, false, true, false, '');

        $pdfFilePath = sys_get_temp_dir() . '/Contract.pdf';
        $pdf->Output($pdfFilePath, 'F');

        $mail->sendMail($pdfFilePath, $obj['emailSeller']);
        $mail->sendMail($pdfFilePath, $obj['emailBuyer']);

    }


    #[Route('/success', name: '_success')]
    public function success(): Response
    {
        return $this->render('contract/success.html.twig');
    }


}
