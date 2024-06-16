<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export\Annotation;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
final class Expose
{
    public string $type = 'string';

    public function __construct(
        public ?string $name = null,
        public ?string $label = null,
        string $type = 'string',
        public ?string $exp = null,
        public ?string $translationDomain = null
    ) {
        if (!\in_array($type, ['string', 'datetime', 'date', 'time', 'integer', 'float', 'duration', 'boolean', 'array'])) {
            throw new \InvalidArgumentException(sprintf('Unknown type "%s" on annotation "%s".', $type, self::class));
        }
        $this->type = $type;
    }
}
