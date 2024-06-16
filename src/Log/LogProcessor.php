<?php

declare(strict_types=1);

namespace App\Log;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

/**
 * @package App\Log
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class LogProcessor
{
    #[AsMonologProcessor]
    public function __invoke(LogRecord $record): LogRecord
    {
        if (\array_key_exists('bundle', $record->context)) {
            $record->extra['channel'] = strtoupper($record->context['bundle']);
        } else {
            $record->extra['channel'] = $record->channel;
        }

        return $record;
    }
}
