<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use DateTimeInterface;

/**
 * @internal this is subject to change
 */
interface InvoiceFormatter
{
    public function getLocale(): string;

    public function setLocale(string $locale): void;

    public function getFormattedDateTime(DateTimeInterface $date): string;

    public function getFormattedTime(DateTimeInterface $date): string;

    public function getFormattedAmount(float $amount): string;

    public function getFormattedMoney(float $amount, ?string $currency, bool $withCurrency = true): string;

    public function getFormattedMonthName(DateTimeInterface $date): string;

    public function getFormattedDuration(int $seconds): string;

    public function getFormattedDecimalDuration(int $seconds): string;

    public function getCurrencySymbol(string $currency): string;
}
