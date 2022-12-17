<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityDate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from OstatDepo method.
 *
 * @psalm-immutable
 */
final class OstatDepoRate implements CbrfEntityDate
{
    private readonly float $days1to7;

    private readonly float $total;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->days1to7 = DataHelper::float('D1_7', $item, .0);
        $this->total = DataHelper::float('total', $item, .0);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getDays1to7(): float
    {
        return $this->days1to7;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}
