<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from Overnight method.
 *
 * @psalm-immutable
 */
final class OvernightRate implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('date', $item);
        $this->rate = DataHelper::float('stavka', $item, .0);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
