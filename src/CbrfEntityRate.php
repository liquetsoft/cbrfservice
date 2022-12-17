<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

/**
 * Interface for DTO that related to some date.
 */
interface CbrfEntityRate extends CbrfEntityDate
{
    public function getRate(): float;
}
