<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use Liquetsoft\CbrfService\Entity\BiCurBacketItem;
use Liquetsoft\CbrfService\Entity\BiCurBaseRate;
use Liquetsoft\CbrfService\Entity\BliquidityRate;
use Liquetsoft\CbrfService\Entity\CurrencyEnum;
use Liquetsoft\CbrfService\Entity\CurrencyRate;
use Liquetsoft\CbrfService\Entity\DepoRate;
use Liquetsoft\CbrfService\Entity\Dv;
use Liquetsoft\CbrfService\Entity\InternationalReserve;
use Liquetsoft\CbrfService\Entity\InternationalReserveWeek;
use Liquetsoft\CbrfService\Entity\KeyRate;
use Liquetsoft\CbrfService\Entity\Mkr;
use Liquetsoft\CbrfService\Entity\OstatDepoRate;
use Liquetsoft\CbrfService\Entity\OstatRate;
use Liquetsoft\CbrfService\Entity\OvernightRate;
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\RepoDebt;
use Liquetsoft\CbrfService\Entity\RepoDebtUSDRate;
use Liquetsoft\CbrfService\Entity\ReutersCurrency;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapDayTotalRate;
use Liquetsoft\CbrfService\Entity\SwapInfoSellItem;
use Liquetsoft\CbrfService\Entity\SwapInfoSellVolItem;
use Liquetsoft\CbrfService\Entity\SwapMonthTotalRate;
use Liquetsoft\CbrfService\Entity\SwapRate;
use Liquetsoft\CbrfService\Exception\CbrfException;

/**
 * Interface for a daily cb RF service.
 */
interface CbrfDaily
{
    /**
     * Returns list of rates for all currencies for set date.
     *
     * @return array<int, CurrencyRate>
     *
     * @throws CbrfException
     */
    public function getCursOnDate(\DateTimeInterface $date): array;

    /**
     * Returns rate for currency with set char code.
     *
     * @throws CbrfException
     */
    public function getCursOnDateByCharCode(\DateTimeInterface $date, string $charCode): ?CurrencyRate;

    /**
     * Returns rate for currency with set numeric code.
     *
     * @throws CbrfException
     */
    public function getCursOnDateByNumericCode(\DateTimeInterface $date, int $numericCode): ?CurrencyRate;

    /**
     * List of all currencies that allowed on cbrf service.
     *
     * @return array<int, CurrencyEnum>
     *
     * @throws CbrfException
     */
    public function enumValutes(bool $seld = false): array;

    /**
     * Returns enum for currency with set char code.
     *
     * @throws CbrfException
     */
    public function enumValuteByCharCode(string $charCode, bool $seld = false): ?CurrencyEnum;

    /**
     * Returns enum for currency with set numeric code.
     *
     * @throws CbrfException
     */
    public function enumValuteByNumericCode(int $numericCode, bool $seld = false): ?CurrencyEnum;

    /**
     * Latest per day date and time of publication.
     *
     * @throws CbrfException
     */
    public function getLatestDateTime(): \DateTimeInterface;

    /**
     * Latest per day date and time of seld.
     *
     * @throws CbrfException
     */
    public function getLatestDateTimeSeld(): \DateTimeInterface;

    /**
     * Latest per month date and time of publication.
     *
     * @throws CbrfException
     */
    public function getLatestDate(): \DateTimeInterface;

    /**
     * Latest per month date and time of seld.
     *
     * @throws CbrfException
     */
    public function getLatestDateSeld(): \DateTimeInterface;

    /**
     * Returns rate dynamic for set currency within set dates.
     *
     * @return array<int, CurrencyRate>
     *
     * @throws CbrfException
     */
    public function getCursDynamic(\DateTimeInterface $from, \DateTimeInterface $to, CbrfEntityCurrencyInternal $currency): array;

    /**
     * Returns key rate dynamic within set dates.
     *
     * @return array<int, KeyRate>
     *
     * @throws CbrfException
     */
    public function keyRate(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns list of presious metals prices within set dates.
     *
     * @return array<int, PreciousMetalRate>
     *
     * @throws CbrfException
     */
    public function dragMetDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns list of swap rates within set dates.
     *
     * @return array<int, SwapRate>
     *
     * @throws CbrfException
     */
    public function swapDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns list depo dynamic items within set dates.
     *
     * @return array<int, DepoRate>
     *
     * @throws CbrfException
     */
    public function depoDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns the dynamic of balances of funds items within set dates.
     *
     * @return array<int, OstatRate>
     *
     * @throws CbrfException
     */
    public function ostatDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns the banks deposites at bank of Russia.
     *
     * @return array<int, OstatDepoRate>
     *
     * @throws CbrfException
     */
    public function ostatDepo(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns international valute reseves of Russia for month.
     *
     * @return array<int, InternationalReserve>
     *
     * @throws CbrfException
     */
    public function mrrf(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns international valute reseves of Russia for week.
     *
     * @return array<int, InternationalReserveWeek>
     *
     * @throws CbrfException
     */
    public function mrrf7d(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns operations saldo.
     *
     * @return array<int, Saldo>
     *
     * @throws CbrfException
     */
    public function saldo(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns Ruonia index.
     *
     * @return array<int, RuoniaIndex>
     *
     * @throws CbrfException
     */
    public function ruoniaSV(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns Ruonia bid.
     *
     * @return array<int, RuoniaBid>
     *
     * @throws CbrfException
     */
    public function ruonia(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns inter banks credit market bids.
     *
     * @return array<int, Mkr>
     *
     * @throws CbrfException
     */
    public function mkr(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns requirements for credit organisations.
     *
     * @return array<int, Dv>
     *
     * @throws CbrfException
     */
    public function dv(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns debts of credit organisations.
     *
     * @return array<int, RepoDebt>
     *
     * @throws CbrfException
     */
    public function repoDebt(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns list of Reuters currencies.
     *
     * @return array<int, ReutersCurrency>
     *
     * @throws CbrfException
     */
    public function enumReutersValutes(\DateTimeInterface $date): array;

    /**
     * Returns list of Reuters rates for all currencies for set date.
     *
     * @return array<int, ReutersCurrencyRate>
     *
     * @throws CbrfException
     */
    public function getReutersCursOnDate(\DateTimeInterface $date): array;

    /**
     * Returns rates of overnight loans.
     *
     * @return array<int, OvernightRate>
     *
     * @throws CbrfException
     */
    public function overnight(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns rates for currency swap.
     *
     * @return array<int, SwapDayTotalRate>
     *
     * @throws CbrfException
     */
    public function swapDayTotal(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns rates for currency swap by eur and usd.
     *
     * @return array<int, SwapMonthTotalRate>
     *
     * @throws CbrfException
     */
    public function swapMonthTotal(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns conditions for currency swap.
     *
     * @return array<int, SwapInfoSellItem>
     *
     * @throws CbrfException
     */
    public function swapInfoSell(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns sell volume for currency swap.
     *
     * @return array<int, SwapInfoSellVolItem>
     *
     * @throws CbrfException
     */
    public function swapInfoSellVol(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns banks liquidity.
     *
     * @return array<int, BliquidityRate>
     *
     * @throws CbrfException
     */
    public function bLiquidity(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns bi currency backet price.
     *
     * @return array<int, BiCurBaseRate>
     *
     * @throws CbrfException
     */
    public function biCurBase(\DateTimeInterface $from, \DateTimeInterface $to): array;

    /**
     * Returns bi currency backet structure.
     *
     * @return array<int, BiCurBacketItem>
     *
     * @throws CbrfException
     */
    public function biCurBacket(): array;

    /**
     * Returns repo debts.
     *
     * @return array<int, RepoDebtUSDRate>
     *
     * @throws CbrfException
     */
    public function repoDebtUSD(\DateTimeInterface $from, \DateTimeInterface $to): array;
}
