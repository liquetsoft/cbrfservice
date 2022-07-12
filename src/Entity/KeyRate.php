<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from KeyRate method.
 */
class KeyRate
{
    private DateTimeInterface $date;

    private float $rate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DT', $item);
        $this->rate = DataHelper::float('Rate', $item, .0);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
