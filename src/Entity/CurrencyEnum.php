<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityCurrencyInternal;
use Liquetsoft\CbrfService\Helper\DataHelper;

/**
 * DTO that represents currency from currencies vocabulary.
 *
 * @psalm-immutable
 */
final class CurrencyEnum implements CbrfEntityCurrencyInternal
{
    private readonly string $internalCode;

    private readonly string $name;

    private readonly string $engName;

    private readonly int $nom;

    private readonly string $commonCode;

    private readonly int $numericCode;

    private readonly string $charCode;

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

    #[\Override]
    public function getInternalCode(): string
    {
        return $this->internalCode;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name;
    }

    public function getEngName(): string
    {
        return $this->engName;
    }

    #[\Override]
    public function getNom(): int
    {
        return $this->nom;
    }

    public function getCommonCode(): string
    {
        return $this->commonCode;
    }

    #[\Override]
    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    #[\Override]
    public function getCharCode(): string
    {
        return $this->charCode;
    }
}
