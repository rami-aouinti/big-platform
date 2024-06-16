<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Spreadsheet;

/**
 * @package App\Crm\Transport\API\Export\Spreadsheet
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ColumnDefinition
{
    private $accessor;

    private string $translationDomain = 'messages';

    public function __construct(
        private readonly string $label,
        private readonly string $type,
        callable $accessor
    ) {
        $this->accessor = $accessor;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAccessor(): callable
    {
        return $this->accessor;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }
}
