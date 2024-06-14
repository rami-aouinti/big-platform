<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Types;

/**
 * @package App\Doctrine
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class UTCDateTimeType extends DateTimeType
{
    private static ?\DateTimeZone $utc = null;

    /**
     * @param T $value
     * @return (T is null ? null : string)
     * @template T<\DateTime>
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof \DateTime) {
            $value = clone $value;
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public static function getUtc(): \DateTimeZone
    {
        if (self::$utc === null) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    /**
     * @param mixed $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\DateTime
    {
        if ($value === null || $value instanceof \DateTime) {
            return $value;
        }

        if (\is_string($value)) {
            $converted = \DateTime::createFromFormat(
                $platform->getDateTimeFormatString(),
                $value,
                self::getUtc()
            );

            if ($converted !== false) {
                return $converted;
            }
        }

        throw ConversionException::conversionFailedFormat(
            $value,
            Types::DATETIME_MUTABLE,
            $platform->getDateTimeFormatString()
        );
    }
}
