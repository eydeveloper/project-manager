<?php

declare(strict_types=1);

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/oauth/facebook')]
class FacebookController extends AbstractController
{
    /**
     * Подключение к аккаунту Facebook.
     *
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    #[Route(name: 'oauth.facebook')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('facebook_main')
            ->redirect(['public_profile'], []);
    }

    /**
     * Редирект на главную страницу после авторизации через аккаунт Facebook.
     *
     * @return RedirectResponse
     */
    #[Route('/check', name: 'oauth.facebook_check')]
    public function check(): RedirectResponse
    {
        return $this->redirectToRoute('home');
    }
}
