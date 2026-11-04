<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data submitted through the contact form.
 *
 * Not readonly: the Form component writes the submitted values into it.
 */
final class ContactMessage
{
    public function __construct(
        #[Assert\NotBlank(message: 'Veuillez indiquer votre nom.')]
        #[Assert\Length(max: 100, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
        public ?string $name = null,

        #[Assert\NotBlank(message: 'Veuillez indiquer votre adresse e-mail.')]
        #[Assert\Email(message: 'Cette adresse e-mail n\'est pas valide.')]
        public ?string $email = null,

        #[Assert\NotBlank(message: 'Veuillez indiquer un sujet.')]
        #[Assert\Length(max: 150, maxMessage: 'Le sujet ne peut pas dépasser {{ limit }} caractères.')]
        public ?string $subject = null,

        #[Assert\NotBlank(message: 'Veuillez écrire un message.')]
        #[Assert\Length(
            min: 10,
            max: 5000,
            minMessage: 'Le message doit contenir au moins {{ limit }} caractères.',
            maxMessage: 'Le message ne peut pas dépasser {{ limit }} caractères.',
        )]
        public ?string $message = null,
    ) {
    }
}
