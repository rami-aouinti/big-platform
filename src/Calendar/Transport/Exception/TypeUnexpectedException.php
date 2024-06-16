<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Calendar\Transport\Exception;

use App\Calendar\Transport\Exception\Base\BaseTypeException;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class TypeUnexpectedException extends BaseTypeException
{
    public const TEXT_PLACEHOLDER = 'Unexpected type "%s" given. "%s" expected.';

    public const TEXT_PLACEHOLDER_WITHOUT_TYPE = 'Unexpected type given. "%s" expected.';

    public function __construct(string $expected, ?string $given = null)
    {
        $messageNonVerbose = $given === null ?
            sprintf(self::TEXT_PLACEHOLDER_WITHOUT_TYPE, $expected) :
            sprintf(self::TEXT_PLACEHOLDER, $given, $expected);

        parent::__construct($messageNonVerbose);
    }
}
