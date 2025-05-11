<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from GetReutersCursOnDate method.
 *
 * @psalm-immutable
 */
final class ReutersCurrencyRate implements CbrfEntityRate
{
    private readonly float $rate;

    private readonly int $dir;

    private readonly int $numericCode;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item, \DateTimeInterface $date)
    {
        $this->rate = DataHelper::float('val', $item, .0);
        $this->dir = DataHelper::int('dir', $item, 1);
        $this->numericCode = DataHelper::int('num_code', $item, 0);
        $this->date = $date;
    }

    #[\Override]
    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDir(): int
    {
        return $this->dir;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
