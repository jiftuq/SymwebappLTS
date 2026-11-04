<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testHomePageRenders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bienvenue sur SymwebappLTS');
        self::assertSelectorExists('nav.site-nav a[aria-current="page"][href="/"]');
    }

    public function testAboutPageIsReachableFromNavigation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->clickLink('À propos');

        self::assertResponseIsSuccessful();
        self::assertRouteSame('app_about');
        self::assertSelectorTextContains('h1', 'À propos');
    }
}
