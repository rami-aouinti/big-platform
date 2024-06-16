<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

interface DragAndDropSource
{
    public function getTitle(): string;

    public function getTranslationDomain(): string;

    public function getRoute(): string;

    /**
     * @return array<string, string>
     */
    public function getRouteParams(): array;

    /**
     * @return array<string, string>
     */
    public function getRouteReplacer(): array;

    public function getMethod(): string;

    /**
     * @return DragAndDropEntry[]
     */
    public function getEntries(): array;

    /**
     * If you want to customize the item rendering, you have to return a path to your include here.
     */
    public function getBlockInclude(): ?string;
}
