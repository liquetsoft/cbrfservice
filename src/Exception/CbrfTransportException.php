<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Exception;

/**
 * Class for a transport exceptions.
 */
final class CbrfTransportException extends CbrfException
{
    private readonly string $method;

    private readonly array $params;

    public function __construct(string $method, array $params = [], ?\Throwable $previous = null)
    {
        $this->method = $method;
        $this->params = $params;

        if ($previous) {
            $message = sprintf("Method '%s' query failed: '%s'", $method, $previous->getMessage());
        } else {
            $message = sprintf("Method '%s' query failed", $method);
        }

        parent::__construct($message, 0, $previous);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
