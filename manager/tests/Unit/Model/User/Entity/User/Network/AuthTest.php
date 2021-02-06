<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::signUpByNetwork(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $network = 'vk',
            $identity = '0000001'
        );

        self::assertTrue($user->isActive());
        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertTrue($user->getRole()->isUser());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = $networks->first());
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
    }
}
