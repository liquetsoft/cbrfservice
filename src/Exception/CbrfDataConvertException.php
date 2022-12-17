<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Exception;

/**
 * Class for exceptions related to data converting with DataHelper.
 */
final class CbrfDataConvertException extends CbrfException
{
    public function __construct(string $from, string $to, ?\Throwable $previous = null)
    {
        if ($previous) {
            $message = sprintf(
                "Can't convert value from '%s' to '%s': %s",
                $from,
                $to,
                $previous->getMessage()
            );
        } else {
            $message = sprintf(
                "Can't convert value from '%s' to '%s'",
                $from,
                $to
            );
        }

        parent::__construct($message, 0, $previous);
    }
}
