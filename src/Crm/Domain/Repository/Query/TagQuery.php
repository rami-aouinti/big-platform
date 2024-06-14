<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

class TagQuery extends BaseQuery
{
    use VisibilityTrait;

    public const TAG_ORDER_ALLOWED = ['name', 'amount'];

    public function __construct()
    {
        $this->setDefaults([
            'orderBy' => 'name',
            'visibility' => VisibilityInterface::SHOW_VISIBLE,
        ]);
    }
}
