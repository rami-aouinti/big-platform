<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Calendar;

interface DragAndDropEntry
{
    /**
     * Data to be passed to the API call.
     *
     * @return array<string, mixed>
     */
    public function getData(): array;

    /**
     * Returns the title for this entry.
     */
    public function getTitle(): string;

    /**
     * Returns the color for this entry.
     */
    public function getColor(): string;

    /**
     * The block to use for rendering the entry.
     */
    public function getBlockName(): ?string;
}
