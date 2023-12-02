<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from SwapDynamic method.
 *
 * @psalm-immutable
 */
final class SwapRate
{
    private readonly \DateTimeInterface $dateBuy;

    private readonly \DateTimeInterface $dateSell;

    private readonly float $baseRate;

    private readonly float $tir;

    private readonly float $rate;

    private readonly int $currency;

    public function __construct(array $item)
    {
        $this->dateBuy = DataHelper::dateTime('DateBuy', $item);
        $this->dateSell = DataHelper::dateTime('DateSell', $item);
        $this->baseRate = DataHelper::float('BaseRate', $item, .0);
        $this->tir = DataHelper::float('TIR', $item, .0);
        $this->rate = DataHelper::float('Stavka', $item, .0);
        $this->currency = DataHelper::int('Currency', $item, 0);
    }

    public function getDateBuy(): \DateTimeInterface
    {
        return $this->dateBuy;
    }

    public function getDateSell(): \DateTimeInterface
    {
        return $this->dateSell;
    }

    public function getBaseRate(): float
    {
        return $this->baseRate;
    }

    public function getTIR(): float
    {
        return $this->tir;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getCurrency(): int
    {
        return $this->currency;
    }
}
