<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use App\Security\UserIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    /**
     * Страница профиля пользователя.
     *
     * @return Response
     */
    #[Route('/profile', name: 'profile')]
    public function show(): Response
    {
        /** @var UserIdentity $identity */
        $identity = $this->getUser();

        $user = $this->users->findDetail($identity->getId());

        return $this->render('app/profile/show.html.twig', compact('user'));
    }
}
