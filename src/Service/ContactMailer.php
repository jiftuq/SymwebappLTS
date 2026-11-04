<?php

namespace App\Service;

use App\Model\ContactMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Forwards a contact form submission to the site owner by e-mail.
 */
final readonly class ContactMailer
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire(param: 'app.contact_email')]
        private string $contactEmail,
    ) {
    }

    public function send(ContactMessage $contact): void
    {
        $email = (new Email())
            ->from($this->contactEmail)
            ->to($this->contactEmail)
            ->replyTo(new Address((string) $contact->email, (string) $contact->name))
            ->subject('[Contact] '.$contact->subject)
            ->text(\sprintf(
                "Nouveau message depuis le formulaire de contact\n\nNom : %s\nE-mail : %s\nSujet : %s\n\n%s\n",
                $contact->name,
                $contact->email,
                $contact->subject,
                $contact->message,
            ));

        $this->mailer->send($email);
    }
}
