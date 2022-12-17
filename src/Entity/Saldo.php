<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from saldo method.
 *
 * @psalm-immutable
 */
final class Saldo implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

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
