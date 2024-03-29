<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from BiCurBacket method.
 *
 * @psalm-immutable
 */
final class BiCurBacketItem
{
    private readonly \DateTimeInterface $date;

    private readonly float $usd;

    private readonly float $eur;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->usd = DataHelper::float('USD', $item, .0);
        $this->eur = DataHelper::float('EUR', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
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
