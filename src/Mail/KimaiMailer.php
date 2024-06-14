<?php

declare(strict_types=1);

namespace App\Mail;

use App\Configuration\MailConfiguration;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

final class KimaiMailer implements MailerInterface
{
    public function __construct(
        private readonly MailConfiguration $configuration,
        private readonly MailerInterface $mailer
    ) {
    }

    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        if ($message instanceof Email && \count($message->getFrom()) === 0) {
            $message->from($this->configuration->getFromAddress());
        }

        $this->mailer->send($message);
    }
}
