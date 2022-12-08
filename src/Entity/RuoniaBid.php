<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from ruonia method.
 *
 * @psalm-immutable
 */
class RuoniaBid implements Rate
{
    private \DateTimeInterface $date;

    private float $rate;

    private float $dealsVolume;

    private \DateTimeInterface $dateUpdate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->rate = DataHelper::float('ruo', $item, .0);
        $this->dealsVolume = DataHelper::float('vol', $item, .0);
        $this->dateUpdate = DataHelper::dateTime('DateUpdate', $item);
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDealsVolume(): float
    {
        return $this->dealsVolume;
    }

    public function getDateUpdate(): \DateTimeInterface
    {
        return $this->dateUpdate;
    }
}
