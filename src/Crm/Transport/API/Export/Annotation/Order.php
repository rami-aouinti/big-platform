<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Annotation;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Order
{
    public function __construct(
        public array $order = []
    ) {
    }
}
