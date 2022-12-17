<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityCurrency;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from EnumReutersValutes method.
 *
 * @psalm-immutable
 */
final class ReutersCurrency implements CbrfEntityCurrency
{
    private readonly string $charCode;

    private readonly string $name;

    private readonly string $nameEn;

    private readonly int $numericCode;

    public function __construct(array $item)
    {
        $this->charCode = DataHelper::charCode('char_code', $item);
        $this->name = DataHelper::string('Title_ru', $item, '');
        $this->nameEn = DataHelper::string('Title_en', $item, '');
        $this->numericCode = DataHelper::int('num_code', $item);
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
}
