<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\DataTransformer;

use App\Utils\Duration;
use App\Validator\Constraints\Duration as DurationConstraint;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @package App\Crm\Transport\Form\DataTransformer
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class DurationStringToSecondsTransformer implements DataTransformerInterface
{
    /**
     * @param int $intToFormat
     */
    public function transform(mixed $intToFormat): ?string
    {
        try {
            return (new Duration())->format($intToFormat);
        } catch (\Exception | \TypeError $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }

    /**
     * @param string|null $formatToInt
     */
    public function reverseTransform(mixed $formatToInt): ?int
    {
        if ($formatToInt === null) {
            return null;
        }

        if (empty($formatToInt)) {
            return 0;
        }

        // we need this one here, because the data transformer is executed BEFORE the constraint is called
        if (!preg_match((new DurationConstraint())->pattern, $formatToInt)) {
            throw new TransformationFailedException('Invalid duration format given');
        }

        try {
            $seconds = (new Duration())->parseDurationString($formatToInt);

            // DateTime throws if a duration with too many seconds is passed and an amount of so
            // many seconds is likely not required in a time-tracking application ;-)
            if ($seconds > 315360000000000) {
                throw new TransformationFailedException('Maximum duration exceeded.');
            }

            return $seconds;
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
