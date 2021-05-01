<?php

declare(strict_types=1);

namespace App\Widget\User;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoleWidget extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_role', [$this, 'role'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param Environment $twig
     * @param string $role
     * @return string
     * @throws LoaderError|RuntimeError|SyntaxError
     */
    public function role(Environment $twig, string $role): string
    {
        return $twig->render('widget/user/role.html.twig', compact('role'));
    }
}
