<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/signup')]
class SignUpController extends AbstractController
{
    private LoggerInterface $logger;
    private UserFetcher $users;
    private TranslatorInterface $translator;

    public function __construct(LoggerInterface $logger, UserFetcher $users, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->users = $users;
        $this->translator = $translator;
    }

    /**
     * Запрос на регистрацию пользователя.
     *
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return RedirectResponse|Response
     */
    #[Route(name: 'auth.signup')]
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
            } catch (DomainException $exception) {
                $this->addFlash('error', $this->translator->trans($exception->getMessage(), domain: 'exceptions'));
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Подтверждение регистрации пользователя.
     *
     * @param string $token
     * @param Request $request
     * @param SignUp\Confirm\ByToken\Handler $handler
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserProviderInterface $userProvider
     * @param LoginFormAuthenticator $authenticator
     * @return RedirectResponse|Response|null
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/{token}', name: 'auth.signup.confirm')]
    public function confirm(
        string $token,
        Request $request,
        SignUp\Confirm\ByToken\Handler $handler,
        GuardAuthenticatorHandler $guardHandler,
        UserProviderInterface $userProvider,
        LoginFormAuthenticator $authenticator
    ): RedirectResponse|Response|null
    {
        if (!$user = $this->users->findBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Некорректный или уже использованный токен.');
            return $this->redirectToRoute('auth.signup');
        }

        $command = new SignUp\Confirm\ByToken\Command($token);

        try {
            $handler->handle($command);
            return $guardHandler->authenticateUserAndHandleSuccess(
                $userProvider->loadUserByUsername($user->email),
                $request,
                $authenticator,
                'main'
            );
        } catch (DomainException $exception) {
            $this->addFlash('error', $this->translator->trans($exception->getMessage(), domain: 'exceptions'));
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            return $this->redirectToRoute('auth.signup');
        }
    }
}
