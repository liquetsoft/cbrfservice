<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from ruonia method.
 *
 * @psalm-immutable
 */
final class RuoniaBid implements CbrfEntityRate
{
    private readonly \DateTimeInterface $date;

    private readonly float $rate;

    private readonly float $dealsVolume;

    private readonly \DateTimeInterface $dateUpdate;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('D0', $item);
        $this->rate = DataHelper::float('ruo', $item, .0);
        $this->dealsVolume = DataHelper::float('vol', $item, .0);
        $this->dateUpdate = DataHelper::dateTime('DateUpdate', $item);
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    #[\Override]
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
