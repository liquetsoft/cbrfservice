<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from DV method.
 */
class Dv implements ItemWithDate
{
    private float $vOvern;

    private float $vLomb;

    private float $vIDay;

    private float $vOther;

    private float $vGold;

    private DateTimeInterface $vIDate;

    private DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->vOvern = DataHelper::float('VOvern', $item, .0);
        $this->vLomb = DataHelper::float('VLomb', $item, .0);
        $this->vIDay = DataHelper::float('VIDay', $item, .0);
        $this->vOther = DataHelper::float('VOther', $item, .0);
        $this->vGold = DataHelper::float('Vol_Gold', $item, .0);
        $this->vIDate = DataHelper::dateTime('VIDate', $item);
        $this->date = DataHelper::dateTime('Date', $item);
    }

    public function getVOvern(): float
    {
        return $this->vOvern;
    }

    public function getVLomb(): float
    {
        return $this->vLomb;
    }

    public function getVIDay(): float
    {
        return $this->vIDay;
    }

    public function getVOther(): float
    {
        return $this->vOther;
    }

    public function getVGold(): float
    {
        return $this->vGold;
    }

    public function getVIDate(): DateTimeInterface
    {
        return $this->vIDate;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
