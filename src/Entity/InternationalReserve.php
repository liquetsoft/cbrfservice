<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from mrrf method.
 *
 * @psalm-immutable
 */
final class InternationalReserve implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->rate = DataHelper::float('p1', $item, .0);
    }

    #[\Override]
    public function getRate(): float
    {
        return $this->rate;
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
