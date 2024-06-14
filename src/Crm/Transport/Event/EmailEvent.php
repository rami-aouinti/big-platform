<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

class EmailEvent extends Event
{
    public function __construct(
        private Email $email
    ) {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
