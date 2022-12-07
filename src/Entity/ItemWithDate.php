<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

/**
 * Interface for DTO that related to some date.
 */
interface ItemWithDate
{
    public function getDate(): \DateTimeInterface;
}
