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

use App\Calendar\Transport\Exception\Base\BaseClassException;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class ClassNotSetException extends BaseClassException
{
    public const TEXT_PLACEHOLDER = '%s not set ("%s").';

    public function __construct(string $case, string $message)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $case, $message);

        parent::__construct($messageNonVerbose);
    }
}
