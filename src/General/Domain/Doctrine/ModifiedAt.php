<?php

declare(strict_types=1);

namespace App\General\Domain\Doctrine;

interface ModifiedAt
{
    public function setModifiedAt(\DateTimeImmutable $dateTime): void;
}
