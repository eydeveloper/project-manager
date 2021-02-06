<?php

namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '0000001'
        );

        self::assertTrue($user->isActive());
        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = $networks->first());
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
    }

    public function testAlready()
    {
        $user = new User(
            Id::next(),
            new \DateTimeImmutable()
        );

        $user->signUpByNetwork(
            $network = 'vk',
            $identity = '0000001'
        );

        $this->expectExceptionMessage('User is already signed up.');

        $user->signUpByNetwork($network, $identity);
    }
}
