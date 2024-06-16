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

use App\Calendar\Transport\Exception\Base\BaseDirectoryException;

/**
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
final class DirectoryNotFoundException extends BaseDirectoryException
{
    public const TEXT_PLACEHOLDER = 'Directory "%s" not found.';

    public function __construct(string $directory)
    {
        $messageNonVerbose = sprintf(self::TEXT_PLACEHOLDER, $directory);

        parent::__construct($messageNonVerbose);
    }
}
