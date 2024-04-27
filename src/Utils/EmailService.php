<?php
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
class EmailService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function envoyerEmail()
    {

        $email = (new Email())
            ->from('latifabenzaied23@gmail.com')
            ->to('latifa.benzaied@esprit.tn')
            ->subject('Sujet de l\'e-mail')
            ->text('Contenu de l\'e-mail');


        $this->mailer->send($email);
    }
}