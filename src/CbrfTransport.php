<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use Liquetsoft\CbrfService\Exception\CbrfTransportException;

/**
 * Interface for transport object.
 */
interface CbrfTransport
{
    /**
     * Creates and sends query to the service.
     *
     * @throws CbrfTransportException
     */
    public function query(string $method, ?array $params = null): array;
}
