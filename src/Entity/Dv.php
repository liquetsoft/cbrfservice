<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityDate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from DV method.
 *
 * @psalm-immutable
 */
final class Dv implements CbrfEntityDate
{
    private readonly float $vOvern;

    private readonly float $vLomb;

    private readonly float $vIDay;

    private readonly float $vOther;

    private readonly float $vGold;

    private readonly \DateTimeInterface $vIDate;

    private readonly \DateTimeInterface $date;

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

    public function getVIDate(): \DateTimeInterface
    {
        return $this->vIDate;
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
