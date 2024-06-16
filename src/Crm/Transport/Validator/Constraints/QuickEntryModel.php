<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class QuickEntryModel extends Constraint
{
    public const ACTIVITY_REQUIRED = 'quick-entry-model-01';
    public const PROJECT_REQUIRED = 'quick-entry-model-02';

    public string $messageActivityRequired = 'An activity needs to be selected.';
    public string $messageProjectRequired = 'A project needs to be selected.';
}
