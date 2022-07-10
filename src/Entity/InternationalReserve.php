<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from mrrf method.
 */
class InternationalReserve
{
    private DateTimeInterface $date;

    private float $reserves;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->reserves = DataHelper::float('p1', $item, .0);
    }

    public function getReserves(): float
    {
        return $this->reserves;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
