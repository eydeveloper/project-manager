<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class NewEmailTokenSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Метод отправляет письмо подтверждения смены электронной почты пользователю.
     *
     * @param Email $email
     * @param string $token
     */
    public function send(Email $email, string $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Подтверждение смены электронной почты')
            ->htmlTemplate('mail/user/email.html.twig')
            ->context(['token' => $token]);

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface) {
            throw new TransportException('Не удалось отправить письмо.');
        }
    }
}
