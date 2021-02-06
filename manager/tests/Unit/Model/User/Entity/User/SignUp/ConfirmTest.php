<?php

namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->confirmSignUp();

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());
        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $user->confirmSignUp();

        $this->expectExceptionMessage('User is already confirmed.');
        $user->confirmSignUp();
    }
}
