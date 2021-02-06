<?php

namespace App\Tests\Unit\Model\User\Entity\User;

use App\Model\User\Entity\User\Role;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        self::assertTrue($user->getRole()->isUser());

        $user->changeRole(Role::admin());

        self::assertTrue($user->getRole()->isAdmin());
        self::assertFalse($user->getRole()->isUser());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('Role is already same.');
        $user->changeRole(Role::user());
    }
}