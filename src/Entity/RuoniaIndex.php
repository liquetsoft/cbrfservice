<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityDate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from ruoniaSV method.
 *
 * @psalm-immutable
 */
final class RuoniaIndex implements CbrfEntityDate
{
    private readonly \DateTimeInterface $date;

    private readonly float $index;

    private readonly float $average1Month;

    private readonly float $average3Month;

    private readonly float $average6Month;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DT', $item);
        $this->index = DataHelper::float('RUONIA_Index', $item, .0);
        $this->average1Month = DataHelper::float('RUONIA_AVG_1M', $item, .0);
        $this->average3Month = DataHelper::float('RUONIA_AVG_3M', $item, .0);
        $this->average6Month = DataHelper::float('RUONIA_AVG_6M', $item, .0);
    }

    #[\Override]
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getIndex(): float
    {
        return $this->index;
    }

    public function getAverage1Month(): float
    {
        return $this->average1Month;
    }

    public function getAverage3Month(): float
    {
        return $this->average3Month;
    }

    public function getAverage6Month(): float
    {
        return $this->average6Month;
    }
}
