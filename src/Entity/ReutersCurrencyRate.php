<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Entity;

use DateTimeInterface;

/**
 * DTO that represents response item from GetCursOnDate method.
 */
class ReutersCurrencyRate
{
    private string $chCode = '';

    private string $nameRu = '';

    private string $nameEn = '';

    private int $code = 0;

    private float $curs = .0;

    private int $dir = 0;

    private DateTimeInterface $date;

    public function __construct(array $item, DateTimeInterface $date)
    {
        $this->chCode = strtoupper(trim($item['char_code'] ?? ''));
        $this->nameRu = trim($item['Title_ru'] ?? '');
        $this->nameEn = trim($item['Title_en'] ?? '');
        $this->code = (int)($item['num_code'] ?? 0);
        $this->curs = (float)($item['val'] ?? .0);
        $this->dir = (int)($item['dir'] ?? 0);
        $this->date = $date;
    }

    public function getChCode(): string
    {
        return $this->chCode;
    }

    public function getNameRu(): string
    {
        return $this->nameRu;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getCurs(): float
    {
        return $this->curs;
    }

    public function getDir(): int
    {
        return $this->dir;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
