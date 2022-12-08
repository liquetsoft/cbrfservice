<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents currency from currencies vocabulary.
 *
 * @psalm-immutable
 */
class CurrencyEnum implements Currency
{
    private string $internalCode;

    private string $name;

    private string $engName;

    private int $nom;

    private string $commonCode;

    private int $numericCode;

    private string $charCode;

    public function __construct(array $item)
    {
        $this->internalCode = DataHelper::string('Vcode', $item, '');
        $this->name = DataHelper::string('Vname', $item, '');
        $this->engName = DataHelper::string('VEngname', $item, '');
        $this->nom = DataHelper::int('Vnom', $item, 0);
        $this->commonCode = DataHelper::string('VcommonCode', $item, '');
        $this->numericCode = DataHelper::int('VnumCode', $item, 0);
        $this->charCode = DataHelper::charCode('VcharCode', $item, '');
    }

    public function getInternalCode(): string
    {
        return $this->internalCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEngName(): string
    {
        return $this->engName;
    }

    public function getNom(): int
    {
        return $this->nom;
    }

    public function getCommonCode(): string
    {
        return $this->commonCode;
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
