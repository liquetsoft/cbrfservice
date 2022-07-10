<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from saldo method.
 */
class Saldo
{
    private DateTimeInterface $date;

    private float $value;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('Dt', $item);
        $this->value = DataHelper::float('DEADLINEBS', $item, .0);
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
