<?php

declare(strict_types=1);

namespace App\Controller\Profile\OAuth;

use App\Model\User\UseCase\Network\Attach\Command;
use App\Model\User\UseCase\Network\Attach\Handler;
use App\Security\UserIdentity;
use DomainException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/oauth/facebook')]
class FacebookController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Подключение к аккаунту Facebook.
     *
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    #[Route('/attach', name: 'profile.oauth.facebook')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('facebook_attach')
            ->redirect(['public_profile'], []);
    }

    /**
     * Редирект на главную страницу после авторизации через аккаунт Facebook.
     *
     * @param ClientRegistry $clientRegistry
     * @param Handler $handler
     * @return RedirectResponse
     */
    #[Route('/check', name: 'profile.oauth.facebook_check')]
    public function check(ClientRegistry $clientRegistry, Handler $handler): RedirectResponse
    {
        if (!($user = $this->getUser()) || !($user instanceof UserIdentity)) {
            return $this->redirectToRoute('app_login');
        }

        $client = $clientRegistry->getClient('facebook_attach');

        $command = new Command(
            $user->getId(),
            'facebook',
            $client->fetchUser()->getId()
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Facebook успешно привязан к аккаунту.');
        } catch (DomainException $exception) {
            $this->addFlash('error', $exception->getMessage());
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        return $this->redirectToRoute('profile');
    }
}
