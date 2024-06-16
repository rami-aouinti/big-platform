<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\User\Domain\Entity\User;

/**
 * @internal
 */
interface RateInterface
{
    public function getUser(): ?User;

    public function getRate(): float;

    public function getInternalRate(): ?float;

    public function isFixed(): bool;

    public function getScore(): int;
}
