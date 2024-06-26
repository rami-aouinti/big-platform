<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Model;

use App\Crm\Application\Utils\Color;
use JMS\Serializer\Annotation as Serializer;

/**
 * @package App\Crm\Transport\API\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Serializer\ExclusionPolicy('all')]
final class CalendarEvent
{
    /**
     * Calendar entry title
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    private string $title; // @phpstan-ignore-line
    /**
     * Calendar background color
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    private ?string $color = null; // @phpstan-ignore-line
    /**
     * Calendar text color
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'string')]
    private ?string $textColor = null;
    /**
     * If this entry is all-day long
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'boolean')]
    private bool $allDay = false; // @phpstan-ignore-line
    /**
     * Calendar entry start date
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'DateTimeImmutable')]
    private \DateTimeImmutable $start; // @phpstan-ignore-line
    /**
     * Calendar entry end date
     */
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type(name: 'DateTimeImmutable')]
    private \DateTimeImmutable $end; // @phpstan-ignore-line

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setStart(\DateTimeInterface $start): void
    {
        $this->start = \DateTimeImmutable::createFromInterface($start);
    }

    public function setEnd(\DateTimeInterface $end): void
    {
        $this->end = \DateTimeImmutable::createFromInterface($end);
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
        if ($color !== null && $this->textColor === null) {
            $this->textColor = (new Color())->getFontContrastColor($color);
        }
    }

    public function setAllDay(bool $allDay): void
    {
        $this->allDay = $allDay;
    }

    public function setTextColor(?string $textColor): void
    {
        $this->textColor = $textColor;
    }
}
