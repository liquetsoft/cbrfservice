<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use Liquetsoft\CbrfService\CbrfEntityRate;
use Liquetsoft\CbrfService\DataHelper;

/**
 * DTO that represents response item from Repo_debt method.
 *
 * @psalm-immutable
 */
final class RepoDebt implements CbrfEntityRate
{
    private readonly float $rate;

    private readonly float $debtAuc;

    private readonly float $debtFix;

    private readonly \DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->rate = DataHelper::float('debt', $item, .0);
        $this->debtAuc = DataHelper::float('debt_auc', $item, .0);
        $this->debtFix = DataHelper::float('debt_fix', $item, .0);
        $this->date = DataHelper::dateTime('Date', $item);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDebtAuc(): float
    {
        return $this->debtAuc;
    }

    public function getDebtFix(): float
    {
        return $this->debtFix;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
