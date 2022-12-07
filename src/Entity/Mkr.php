<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from mkr method.
 */
class Mkr implements ItemWithDate
{
    private DateTimeInterface $date;

    private int $p1;

    private ?float $d1;

    private ?float $d7;

    private ?float $d30;

    private ?float $d90;

    private ?float $d180;

    private ?float $d360;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('CDate', $item);
        $this->p1 = DataHelper::int('p1', $item, 0);
        $this->d1 = DataHelper::floatOrNull('d1', $item);
        $this->d7 = DataHelper::floatOrNull('d7', $item);
        $this->d30 = DataHelper::floatOrNull('d30', $item);
        $this->d90 = DataHelper::floatOrNull('d90', $item);
        $this->d180 = DataHelper::floatOrNull('d180', $item);
        $this->d360 = DataHelper::floatOrNull('d360', $item);
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getP1(): int
    {
        return $this->p1;
    }

    public function getD1(): ?float
    {
        return $this->d1;
    }

    public function getD7(): ?float
    {
        return $this->d7;
    }

    public function getD30(): ?float
    {
        return $this->d30;
    }

    public function getD90(): ?float
    {
        return $this->d90;
    }

    public function getD180(): ?float
    {
        return $this->d180;
    }

    public function getD360(): ?float
    {
        return $this->d360;
    }
}
