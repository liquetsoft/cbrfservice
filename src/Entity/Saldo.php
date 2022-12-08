<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from saldo method.
 *
 * @psalm-immutable
 */
class Saldo implements Rate
{
    private \DateTimeInterface $date;

    private float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('Dt', $item);
        $this->rate = DataHelper::float('DEADLINEBS', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }
}
