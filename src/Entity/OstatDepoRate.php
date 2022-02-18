<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;
use Marvin255\CbrfService\DataHelper;

/**
 * DTO that represents response item from OstatDepo method.
 */
class OstatDepoRate
{
    private float $days1to7 = .0;

    private float $total = .0;

    private DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->days1to7 = DataHelper::float('D1_7', $item, .0);
        $this->total = DataHelper::float('total', $item, .0);
    }

    public function getDate(): DateTimeInterface
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
