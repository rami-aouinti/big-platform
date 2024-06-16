<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

final class GoogleSource extends CalendarSource
{
    public function __construct(string $id, string $uri, ?string $color = null)
    {
        parent::__construct(CalendarSourceType::GOOGLE, $id, $uri, $color);
    }
}
