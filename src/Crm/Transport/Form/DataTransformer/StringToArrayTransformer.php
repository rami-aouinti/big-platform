<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<array, string>
 */
final readonly class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param non-empty-string $separator
     */
    public function __construct(
        private string $separator = ','
    ) {
    }

    /**
     * Transforms an array of strings to a string.
     *
     * @param array<string> $value
     */
    public function transform(mixed $value): string
    {
        if (empty($value)) {
            return '';
        }

        return implode($this->separator, $value);
    }

    /**
     * Transforms a string to an array of strings.
     *
     * @param string|null $value
     *
     * @throws TransformationFailedException
     *@return array<string>
     */
    public function reverseTransform(mixed $value): array
    {
        // check for empty list
        if ($value === '' || $value === null) {
            return [];
        }

        return array_filter(array_unique(array_map('trim', explode($this->separator, $value))));
    }
}
