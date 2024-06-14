<?php

declare(strict_types=1);

namespace App\Crm\Application\Mail;

use App\Configuration\MailConfiguration;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

/**
 * @package App\Crm\Application\Mail
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final readonly class KimaiMailer implements MailerInterface
{
    public function __construct(
        private MailConfiguration $configuration,
        private MailerInterface $mailer
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
