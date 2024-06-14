<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

/**
 * Can be used to pre-fill form types with: UserRepository::getQueryBuilderForFormType()
 */
final class UserFormTypeQuery extends BaseFormTypeQuery
{
    use VisibilityTrait;
}
