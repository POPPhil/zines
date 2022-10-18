<?php

// Service de géstion d'Email

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private const NAME = 'Zines Administration';
    private const EMAIL = 'no-reply@zines.com';

    private $mailer;

    public function __construct(MailerInterface $mailerInterface)
    {
        $this->mailer = $mailerInterface;
    }

    public function sendEmail(string $to, string $subject, array $options = [])
    {
        $email = new TemplatedEmail();
        $email->from(new Address(self::EMAIL, self::NAME));
        $email->to(new Address($to));
        $email->subject($subject);
        $email->htmlTemplate($options['template']);
        $email->context($options['context']);

        $this->mailer->send($email);
    }
}