<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from SwapInfoSell method.
 *
 * @psalm-immutable
 */
final class SwapInfoSellItem
{
    private readonly SwapInfoSellItemCurrency $currency;

    private readonly \DateTimeInterface $dateBuy;

    private readonly \DateTimeInterface $dateSell;

    private readonly \DateTimeInterface $dateSPOT;

    private readonly int $type;

    private readonly float $baseRate;

    private readonly float $sd;

    private readonly float $tir;

    private readonly float $rate;

    private readonly float $limit;

    public function __construct(array $item)
    {
        $this->currency = DataHelper::enumInt('Currency', $item, SwapInfoSellItemCurrency::class);
        $this->dateBuy = DataHelper::dateTime('DateBuy', $item);
        $this->dateSell = DataHelper::dateTime('DateSell', $item);
        $this->dateSPOT = DataHelper::dateTime('DateSPOT', $item);
        $this->type = DataHelper::int('Type', $item, 0);
        $this->baseRate = DataHelper::float('BaseRate', $item, .0);
        $this->sd = DataHelper::float('SD', $item, .0);
        $this->tir = DataHelper::float('TIR', $item, .0);
        $this->rate = DataHelper::float('Stavka', $item, .0);
        $this->limit = DataHelper::float('limit', $item, .0);
    }

    public function getCurrency(): SwapInfoSellItemCurrency
    {
        return $this->currency;
    }

    public function getDateBuy(): \DateTimeInterface
    {
        return $this->dateBuy;
    }

    public function getDateSell(): \DateTimeInterface
    {
        return $this->dateSell;
    }

    public function getDateSPOT(): \DateTimeInterface
    {
        return $this->dateSPOT;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getBaseRate(): float
    {
        return $this->baseRate;
    }

    public function getSD(): float
    {
        return $this->sd;
    }

    public function getTIR(): float
    {
        return $this->tir;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getLimit(): float
    {
        return $this->limit;
    }
}
