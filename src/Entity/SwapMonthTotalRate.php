<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from SwapMonthTotal method.
 *
 * @psalm-immutable
 */
final class SwapMonthTotalRate implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    private readonly float $usd;

    private readonly float $eur;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->rate = DataHelper::float('RUB', $item, .0);
        $this->usd = DataHelper::float('USD', $item, .0);
        $this->eur = DataHelper::float('EUR', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getUSD(): float
    {
        return $this->usd;
    }

    public function getEUR(): float
    {
        return $this->eur;
    }
}
