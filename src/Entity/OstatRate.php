<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;
use Marvin255\CbrfService\DataHelper;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class OstatRate
{
    private float $moscow = .0;

    private float $russia = .0;

    private DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DateOst', $item);
        $this->moscow = DataHelper::float('InMoscow', $item, .0);
        $this->russia = DataHelper::float('InRuss', $item, .0);
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getMoscow(): float
    {
        return $this->moscow;
    }

    public function getRussia(): float
    {
        return $this->russia;
    }
}
