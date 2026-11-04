<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContactControllerTest extends WebTestCase
{
    public function testContactPageShowsFormAndMap(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Contact');
        self::assertCount(1, $crawler->filter('form textarea[name="contact[message]"]'));
        self::assertStringStartsWith(
            'https://maps.google.com/maps?q=',
            $crawler->filter('.map iframe')->attr('src'),
        );
    }

    public function testInvalidSubmissionShowsErrors(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');
        $client->submitForm('Envoyer', [
            'contact[name]' => '',
            'contact[email]' => 'pas-un-email',
            'contact[subject]' => 'Test',
            'contact[message]' => 'court',
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('.field', 'Veuillez indiquer votre nom.');
        self::assertAnySelectorTextContains('.field ul', 'Cette adresse e-mail n\'est pas valide.');
        self::assertAnySelectorTextContains('.field ul', 'au moins 10 caractères');
        self::assertEmailCount(0);
    }

    public function testValidSubmissionSendsEmailAndRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');
        $client->submitForm('Envoyer', [
            'contact[name]' => 'Jeanne Dupont',
            'contact[email]' => 'jeanne@example.be',
            'contact[subject]' => 'Demande de devis',
            'contact[message]' => 'Bonjour, je voudrais un devis pour un site web.',
        ]);

        self::assertResponseRedirects('/contact');
        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        self::assertEmailHeaderSame($email, 'Subject', '[Contact] Demande de devis');
        self::assertEmailAddressContains($email, 'Reply-To', 'jeanne@example.be');
        self::assertEmailTextBodyContains($email, 'Bonjour, je voudrais un devis');

        $client->followRedirect();
        self::assertSelectorTextContains('.alert--success', 'Votre message a bien été envoyé');
    }
}
