<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Exception;

/**
 * Class for exceptions related to data accessing with DataHelper.
 */
final class CbrfDataAccessException extends CbrfException
{
    public function __construct(string $path = '', string $type = '', ?\Throwable $previous = null)
    {
        if (!empty($path) && !empty($type)) {
            $message = \sprintf("Can't find '%s' value at '%s'", $type, $path);
        } elseif (!empty($path)) {
            $message = \sprintf("Can't find value at '%s'", $path);
        } else {
            $message = "Can't find value";
        }

        parent::__construct($message, 0, $previous);
    }
}
