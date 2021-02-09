<?php

namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignUpController extends AbstractController
{
    private LoggerInterface $logger;
    private TranslatorInterface $translator;

    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    #[Route('/signup', name: 'auth.signup')]
    public function request(Request $request, SignUp\Request\Handler $handler): RedirectResponse|Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email.');
                return $this->redirectToRoute('home');
            } catch (\DomainException $exception) {
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), domain: 'exceptions'));
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/signup/{token}', name: 'auth.signup.confirm')]
    public function confirm(string $token, SignUp\Confirm\Handler $handler): RedirectResponse
    {
        $command = new SignUp\Confirm\Command($token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Email is successfully confirmed.');
            return $this->redirectToRoute('home');
        } catch (\DomainException $exception) {
            $this->addFlash('error', $this->translator->trans($exception->getMessage(), domain: 'exceptions'));
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            return $this->redirectToRoute('home');
        }
    }
}
