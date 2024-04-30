<?php
namespace App\Utils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class EmailService
{


    /**
     * @throws TransportExceptionInterface
     */
    public function envoyerEmail(MailerInterface $mailer) :Response
    {

        $email = (new Email())
            ->from('latifabenzaied23@gmail.com')
            ->to('latifa.benzaied@esprit.tn')
            ->subject('Hello from Symfony Mailer')
            ->text('This is the plain text body.')
            ->html('<p>This is the HTML body.</p>');
        $mailer->send($email);
        return new Response('yasasasasasasaas');
    }

}