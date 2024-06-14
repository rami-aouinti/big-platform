<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Configuration\LocaleService;
use App\Crm\Application\Utils\LocaleFormatter;

/**
 * @package App\Crm\Application\Service\Invoice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class DefaultInvoiceFormatter implements InvoiceFormatter
{
    private ?LocaleFormatter $formatter;

    public function __construct(
        private readonly LocaleService $localeService,
        private string $locale
    ) {
    }

    public function getFormattedDateTime(\DateTimeInterface $date): string
    {
        return (string)$this->getFormatter()->dateShort($date);
    }

    public function getFormattedTime(\DateTimeInterface $date): string
    {
        return (string)$this->getFormatter()->time($date);
    }

    public function getFormattedMonthName(\DateTimeInterface $date): string
    {
        return $this->getFormatter()->monthName($date);
    }

    public function getFormattedMoney(float $amount, ?string $currency, bool $withCurrency = true): string
    {
        return $this->getFormatter()->money($amount, $currency, $withCurrency);
    }

    public function getFormattedDuration(int $seconds): string
    {
        return $this->getFormatter()->duration($seconds);
    }

    public function getFormattedDecimalDuration(int $seconds): string
    {
        return $this->getFormatter()->durationDecimal($seconds);
    }

    public function getCurrencySymbol(string $currency): string
    {
        return $this->getFormatter()->currency($currency);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->formatter = null;
    }

    public function getFormattedAmount(float $amount): string
    {
        return $this->getFormatter()->amount($amount);
    }

    private function getFormatter(): LocaleFormatter
    {
        if ($this->formatter === null) {
            $this->formatter = new LocaleFormatter($this->localeService, $this->locale);
        }

        return $this->formatter;
    }
}
