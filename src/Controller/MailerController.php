<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport;

class MailerController extends AbstractController
{

    public static function sendMail($file,$to): Response
    {
        $transport = Transport::fromDsn('smtp://salhiomar362@gmail.com:pnoavpopklfyybeb@smtp.gmail.com:587');
        $mailer=new Mailer($transport);

        $email = (new Email())
            ->from('salhiomar362@gmail.com')
            ->to($to)
            ->subject('Hello from Symfony Mailer')
            ->attachFromPath($file)
            ->text('This is the plain text body.')
            ->html('<p>This is the HTML body.</p>');

        $mailer->send($email);
        return new Response('Mail sent!');
    }

    public function sendNormalMail($to): Response
    {
        $transport = Transport::fromDsn('smtp://salhiomar362@gmail.com:pnoavpopklfyybeb@smtp.gmail.com:587');
        $mailer=new Mailer($transport);

        $email = (new Email())
            ->from('salhiomar362@gmail.com')
            ->to('omar.salhi.gob@gmail.com')
            ->subject('Hello from Symfony Mailer')
            ->text('This is the plain text body.')
            ->html('<p>This is the HTML body.</p>');

        $mailer->send($email);
        return new Response('Mail sent!');
    }
}
