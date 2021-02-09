<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmTokenSender
{
    private MailerInterface $mailer;
    private array $from;

    public function __construct(MailerInterface $mailer, array $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject('Sign Up Confirmation')
            ->htmlTemplate('mail/user/signup.html.twig')
            ->context(['token' => $token]);

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface) {
            throw new TransportException('Unable to send message.');
        }
    }
}
