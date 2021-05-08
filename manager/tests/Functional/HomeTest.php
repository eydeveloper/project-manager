<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertSame(302, $client->getResponse()->getStatusCode());
        self::assertSame('http://localhost/login', $client->getResponse()->headers->get('Location'));
    }

    public function testSuccess(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@app.test',
            'PHP_AUTH_PW' => 'password',
        ]);
        $crawler = $client->request('GET', '/');

        self::assertSame(200, $client->getResponse()->getStatusCode());
        self::assertStringContainsString('Главная страница', $crawler->filter('h1')->text());
    }
}
