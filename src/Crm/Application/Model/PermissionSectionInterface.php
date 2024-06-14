<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

interface PermissionSectionInterface
{
    /**
     * Returns the section title (which will be translated).
     */
    public function getTitle(): string;

    /**
     * Returns whether the given permission is part of this section
     */
    public function filter(string $permission): bool;
}
