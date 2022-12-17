<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityCurrency;
use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from GetCursOnDate method.
 *
 * @psalm-immutable
 */
final class ReutersCurrencyRate implements CbrfEntityCurrency, CbrfEntityRate
{
    private readonly string $charCode;

    private readonly string $name;

    private readonly string $nameEn;

    private readonly int $numericCode;

    private readonly float $rate;

    private readonly int $dir;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item, \DateTimeInterface $date)
    {
        $this->charCode = DataHelper::charCode('char_code', $item);
        $this->name = DataHelper::string('Title_ru', $item, '');
        $this->nameEn = DataHelper::string('Title_en', $item, '');
        $this->numericCode = DataHelper::int('num_code', $item);
        $this->rate = DataHelper::float('val', $item, .0);
        $this->dir = DataHelper::int('dir', $item, 1);
        $this->date = $date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function getNom(): int
    {
        return 1;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDir(): int
    {
        return $this->dir;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
