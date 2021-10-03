<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;
use Marvin255\CbrfService\DataHelper;

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
        $this->rate = DataHelper::float('Rate', $item);
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
