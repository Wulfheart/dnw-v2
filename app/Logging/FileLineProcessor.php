<?php

namespace App\Logging;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class FileLineProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);

        if (isset($backtrace[5])) {
            // @phpstan-ignore-next-line
            $fileName = $backtrace[5]['file'];

            $record->extra['file'] = match (str_starts_with($fileName, base_path())) {
                true => ltrim(str_replace(base_path(), '', $fileName), '/'),
                false => $fileName,
            };
            // @phpstan-ignore-next-line
            $record->extra['line'] = $backtrace[5]['line'];
        }

        return $record;
    }
}
