<?php

declare(strict_types=1);

namespace App\Crm\Transport\Event;

use App\Crm\Domain\Entity\MetaTableTypeInterface;
use App\Crm\Domain\Repository\Query\BaseQuery;

interface MetaDisplayEventInterface
{
    /**
     * If you want to filter where your meta-field will be displayed, use the query settings.
     */
    public function getQuery(): BaseQuery;

    /**
     * If you want to filter where your meta-field will be displayed, check the current location.
     */
    public function getLocation(): string;

    /**
     * @return MetaTableTypeInterface[]
     */
    public function getFields(): array;

    /**
     * Adds a field that should be displayed.
     */
    public function addField(MetaTableTypeInterface $meta): void;
}
