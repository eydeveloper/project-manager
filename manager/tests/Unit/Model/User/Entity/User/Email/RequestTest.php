<?php

namespace App\Tests\Unit\Model\User\Entity\User\Email;

use App\Model\User\Entity\User\Email;
use App\Model\User\Exception\UserAlreadySameEmail;
use App\Model\User\Exception\UserNotActiveException;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->requestEmailChanging(
            $email = new Email('new@app.test'),
            $token = 'token'
        );

        self::assertEquals($email, $user->getNewEmail());
        self::assertEquals($token, $user->getNewEmailToken());
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectException(UserNotActiveException::class);
        $user->requestEmailChanging(new Email('new@app.test'), 'token');
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->viaEmail($email = new Email('new@app.test'))
            ->confirmed()
            ->build();

        $this->expectException(UserAlreadySameEmail::class);
        $user->requestEmailChanging($email, 'token');
    }
}