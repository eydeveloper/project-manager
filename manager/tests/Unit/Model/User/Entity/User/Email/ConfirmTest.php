<?php

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Model\User\Exception\UserEmailChangingNotRequested;
use App\Model\User\Exception\UserInvalidNewEmailToken;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        $user->confirmEmailChanging($token);

        self::assertEquals($user->getEmail(), $email);
        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $this->expectException(UserEmailChangingNotRequested::class);
        $user->confirmEmailChanging('token');
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            new Email('new@app.test'),
            'token'
        );

        $this->expectException(UserInvalidNewEmailToken::class);
        $user->confirmEmailChanging('invalid');
    }
}