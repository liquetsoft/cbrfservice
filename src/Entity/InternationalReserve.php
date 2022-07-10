<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from mrrf method.
 */
class InternationalReserve
{
    private DateTimeInterface $date;

    private float $value;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->value = DataHelper::float('p1', $item, .0);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
