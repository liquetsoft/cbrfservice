<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from SwapDynamic method.
 */
class SwapRate
{
    private DateTimeInterface $dateBuy;

    private DateTimeInterface $dateSell;

    private float $baseRate;

    private float $tir;

    private float $rate;

    private int $currency;

    public function __construct(array $item)
    {
        $this->dateBuy = DataHelper::dateTime('DateBuy', $item);
        $this->dateSell = DataHelper::dateTime('DateSell', $item);
        $this->baseRate = DataHelper::float('BaseRate', $item, .0);
        $this->tir = DataHelper::float('TIR', $item, .0);
        $this->rate = DataHelper::float('Stavka', $item, .0);
        $this->currency = DataHelper::int('Currency', $item, 0);
    }

    public function getDateBuy(): DateTimeInterface
    {
        return $this->dateBuy;
    }

    public function getDateSell(): DateTimeInterface
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
