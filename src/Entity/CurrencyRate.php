<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityCurrency;
use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents response item from GetCursOnDate method.
 *
 * @psalm-immutable
 */
final class CurrencyRate implements CbrfEntityCurrency, CbrfEntityRate
{
    private readonly string $charCode;

    private readonly string $name;

    private readonly int $numericCode;

    private readonly float $rate;

    private readonly int $nom;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item, \DateTimeInterface $date)
    {
        $this->charCode = DataHelper::charCode('VchCode', $item, '');
        $this->name = DataHelper::string('Vname', $item, '');
        $this->numericCode = DataHelper::int('Vcode', $item, 0);
        $this->rate = DataHelper::float('Vcurs', $item, .0);
        $this->nom = DataHelper::int('Vnom', $item, 0);
        $this->date = $date;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getNom(): int
    {
        return $this->nom;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
