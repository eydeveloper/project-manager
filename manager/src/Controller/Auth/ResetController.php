<?php

namespace App\Controller\Auth;

use App\Model\User\UseCase\Reset;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/reset', name: 'auth.reset')]
    public function request(Request $request, Reset\Request\Handler $handler): RedirectResponse|Response
    {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this->render('app/auth/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reset/{token}', name: 'auth.reset.reset')]
    public function reset(string $token, Request $request, Reset\Reset\Handler $handler): RedirectResponse|Response
    {
        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password if successfully changed.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this->render('app/auth/reset/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
