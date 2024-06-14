<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Validator\Constraint;

/**
 * Extend this class if you want to add dynamic project validation (eg. via a bundle).
 */
#[AutoconfigureTag]
abstract class ProjectConstraint extends Constraint
{
}
