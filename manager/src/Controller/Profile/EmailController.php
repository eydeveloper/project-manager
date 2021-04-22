<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Model\User\UseCase\Email;
use App\Security\UserIdentity;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile/email')]
class EmailController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Запрос на смену электронной почты.
     *
     * @param Request $request
     * @param Email\Request\Handler $handler
     * @return RedirectResponse|Response
     */
    #[Route(name: 'profile.email')]
    public function request(Request $request, Email\Request\Handler $handler): RedirectResponse|Response
    {
        if (!($user = $this->getUser()) || !($user instanceof UserIdentity)) {
            return $this->redirectToRoute('app_login');
        }

        $command = new Email\Request\Command($user->getId());

        $form = $this->createForm(Email\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Письмо с подтверждением отправлено на вашу электронную почту.');
                return $this->redirectToRoute('profile');
            } catch (DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this->render('app/profile/email.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Подтверждение смены электронной почты.
     *
     * @param string $token
     * @param Email\Confirm\Handler $handler
     * @return RedirectResponse
     */
    #[Route('/{token}', name: 'profile.email.confirm')]
    public function confirm(string $token, Email\Confirm\Handler $handler): RedirectResponse
    {
        if (!($user = $this->getUser()) || !($user instanceof UserIdentity)) {
            return $this->redirectToRoute('app_login');
        }

        $command = new Email\Confirm\Command($user->getId(), $token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Электронная почта успешно изменена.');
        } catch (DomainException $exception) {
            $this->addFlash('error', $exception->getMessage());
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        return $this->redirectToRoute('profile');
    }
}
