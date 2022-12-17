<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

/**
 * Interface for DTO that related to some date.
 */
interface CbrfEntityDate
{
    public function getDate(): \DateTimeInterface;
}
