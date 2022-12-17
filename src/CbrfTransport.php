<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

/**
 * Interface for transport object.
 */
interface CbrfTransport
{
    /**
     * Creates and sends query to the service.
     *
     * @param string     $method
     * @param array|null $params
     *
     * @return array
     *
     * @throws CbrfException
     */
    public function query(string $method, ?array $params = null): array;
}
