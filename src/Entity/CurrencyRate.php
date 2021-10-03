<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;
use Marvin255\CbrfService\DataHelper;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class CurrencyRate implements Currency
{
    private string $charCode;

    private string $name;

    private int $numericCode;

    private float $rate;

    private int $nom;

    private DateTimeInterface $date;

    public function __construct(array $item, DateTimeInterface $date)
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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
