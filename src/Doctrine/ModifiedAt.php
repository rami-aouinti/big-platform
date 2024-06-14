<?php

declare(strict_types=1);

namespace App\Doctrine;

interface ModifiedAt
{
    public function setModifiedAt(\DateTimeImmutable $dateTime): void;
}
