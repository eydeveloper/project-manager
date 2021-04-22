<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ResetTokenSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Метод отправляет письмо восстановления пароля.
     *
     * @param Email $email
     * @param ResetToken $token
     */
    public function send(Email $email, ResetToken $token): void
    {
        $message = (new TemplatedEmail())
            ->to($email->getValue())
            ->subject('Восстановление пароля')
            ->htmlTemplate('mail/user/reset.html.twig')
            ->context(['token' => $token->getToken()]);

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface) {
            throw new TransportException('Не удалось отправить письмо.');
        }
    }
}
