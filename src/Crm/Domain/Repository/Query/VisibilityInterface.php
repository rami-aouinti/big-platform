<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

interface VisibilityInterface
{
    public const SHOW_VISIBLE = 1;
    public const SHOW_HIDDEN = 2;
    public const SHOW_BOTH = 3;

    public const ALLOWED_VISIBILITY_STATES = [
        self::SHOW_BOTH,
        self::SHOW_VISIBLE,
        self::SHOW_HIDDEN,
    ];

    public function getVisibility(): int;

    public function setVisibility(int $visibility): void;
}
