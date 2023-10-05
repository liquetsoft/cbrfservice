<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

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
use Liquetsoft\CbrfService\Entity\ReutersCurrency;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapDayTotalRate;
use Liquetsoft\CbrfService\Entity\SwapRate;
use Liquetsoft\CbrfService\Exception\CbrfException;

/**
 * Class for a daily cb RF service.
 */
final class CbrfDaily
{
    private readonly CbrfTransport $transport;

    public function __construct(CbrfTransport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Returns list of rates for all currencies for set date.
     *
     * @return CurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getCursOnDate(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'GetCursOnDate',
            [
                'On_date' => $date,
            ]
        );

        $immutableDate = DataHelper::createImmutableDateTime($date);
        $list = DataHelper::array('ValuteData.ValuteCursOnDate', $res);
        $callback = fn (array $item): CurrencyRate => new CurrencyRate($item, $immutableDate);

        return array_map($callback, $list);
    }

    /**
     * Returns rate for currency with set char code.
     *
     * @throws CbrfException
     */
    public function getCursOnDateByCharCode(\DateTimeInterface $date, string $charCode): ?CurrencyRate
    {
        $list = $this->getCursOnDate($date);

        $return = null;
        foreach ($list as $item) {
            if (strcasecmp($charCode, $item->getCharCode()) === 0) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * Returns rate for currency with set numeric code.
     *
     * @throws CbrfException
     */
    public function getCursOnDateByNumericCode(\DateTimeInterface $date, int $numericCode): ?CurrencyRate
    {
        $list = $this->getCursOnDate($date);

        $return = null;
        foreach ($list as $item) {
            if ($item->getNumericCode() === $numericCode) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * List of all currencies that allowed on cbrf service.
     *
     * @return CurrencyEnum[]
     *
     * @throws CbrfException
     */
    public function enumValutes(bool $seld = false): array
    {
        $res = $this->transport->query(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        return DataHelper::arrayOfItems('ValuteData.EnumValutes', $res, CurrencyEnum::class);
    }

    /**
     * Returns enum for currency with set char code.
     *
     * @throws CbrfException
     */
    public function enumValuteByCharCode(string $charCode, bool $seld = false): ?CurrencyEnum
    {
        $list = $this->enumValutes($seld);

        $return = null;
        foreach ($list as $item) {
            if (strcasecmp($charCode, $item->getCharCode()) === 0) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * Returns enum for currency with set numeric code.
     *
     * @throws CbrfException
     */
    public function enumValuteByNumericCode(int $numericCode, bool $seld = false): ?CurrencyEnum
    {
        $list = $this->enumValutes($seld);

        $return = null;
        foreach ($list as $item) {
            if ($item->getNumericCode() === $numericCode) {
                $return = $item;
                break;
            }
        }

        return $return;
    }

    /**
     * Latest per day date and time of publication.
     *
     * @throws CbrfException
     */
    public function getLatestDateTime(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateTime');

        return DataHelper::dateTime('GetLatestDateTimeResult', $res);
    }

    /**
     * Latest per day date and time of seld.
     *
     * @throws CbrfException
     */
    public function getLatestDateTimeSeld(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateTimeSeld');

        return DataHelper::dateTime('GetLatestDateTimeSeldResult', $res);
    }

    /**
     * Latest per month date and time of publication.
     *
     * @throws CbrfException
     */
    public function getLatestDate(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDate');

        return DataHelper::dateTime('GetLatestDateResult', $res);
    }

    /**
     * Latest per month date and time of seld.
     *
     * @throws CbrfException
     */
    public function getLatestDateSeld(): \DateTimeInterface
    {
        $res = $this->transport->query('GetLatestDateSeld');

        return DataHelper::dateTime('GetLatestDateSeldResult', $res);
    }

    /**
     * Returns rate dynamic for set currency within set dates.
     *
     * @return CurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getCursDynamic(\DateTimeInterface $from, \DateTimeInterface $to, CbrfEntityCurrencyInternal $currency): array
    {
        $res = $this->transport->query(
            'GetCursDynamic',
            [
                'FromDate' => $from,
                'ToDate' => $to,
                'ValutaCode' => $currency->getInternalCode(),
            ]
        );

        $result = [];
        $list = DataHelper::array('ValuteData.ValuteCursDynamic', $res);
        foreach ($list as $item) {
            if (\is_array($item)) {
                $date = DataHelper::dateTime('CursDate', $item);
                $item['Vname'] = $currency->getName();
                $item['VchCode'] = $currency->getCharCode();
                $item['Vcode'] = $currency->getNumericCode();
                $result[] = new CurrencyRate($item, $date);
            }
        }

        return $result;
    }

    /**
     * Returns key rate dynamic within set dates.
     *
     * @return KeyRate[]
     *
     * @throws CbrfException
     */
    public function keyRate(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'KeyRate',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('KeyRate.KR', $res, KeyRate::class);
    }

    /**
     * Returns list of presious metals prices within set dates.
     *
     * @return PreciousMetalRate[]
     *
     * @throws CbrfException
     */
    public function dragMetDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DragMetDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DragMetall.DrgMet', $res, PreciousMetalRate::class);
    }

    /**
     * Returns list of swap rates within set dates.
     *
     * @return SwapRate[]
     *
     * @throws CbrfException
     */
    public function swapDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapDynamic.Swap', $res, SwapRate::class);
    }

    /**
     * Returns list depo dynamic items within set dates.
     *
     * @return DepoRate[]
     *
     * @throws CbrfException
     */
    public function depoDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DepoDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DepoDynamic.Depo', $res, DepoRate::class);
    }

    /**
     * Returns the dynamic of balances of funds items within set dates.
     *
     * @return OstatRate[]
     *
     * @throws CbrfException
     */
    public function ostatDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDynamic',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('OstatDynamic.Ostat', $res, OstatRate::class);
    }

    /**
     * Returns the banks deposites at bank of Russia.
     *
     * @return OstatDepoRate[]
     *
     * @throws CbrfException
     */
    public function ostatDepo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDepo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('OD.odr', $res, OstatDepoRate::class);
    }

    /**
     * Returns international valute reseves of Russia for month.
     *
     * @return InternationalReserve[]
     *
     * @throws CbrfException
     */
    public function mrrf(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mmrf.mr', $res, InternationalReserve::class);
    }

    /**
     * Returns international valute reseves of Russia for week.
     *
     * @return InternationalReserveWeek[]
     *
     * @throws CbrfException
     */
    public function mrrf7d(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf7D',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mmrf7d.mr', $res, InternationalReserveWeek::class);
    }

    /**
     * Returns operations saldo.
     *
     * @return Saldo[]
     *
     * @throws CbrfException
     */
    public function saldo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Saldo',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Saldo.So', $res, Saldo::class);
    }

    /**
     * Returns Ruonia index.
     *
     * @return RuoniaIndex[]
     *
     * @throws CbrfException
     */
    public function ruoniaSV(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'RuoniaSV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('RuoniaSV.ra', $res, RuoniaIndex::class);
    }

    /**
     * Returns Ruonia bid.
     *
     * @return RuoniaBid[]
     *
     * @throws CbrfException
     */
    public function ruonia(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Ruonia',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Ruonia.ro', $res, RuoniaBid::class);
    }

    /**
     * Returns inter banks credit market bids.
     *
     * @return Mkr[]
     *
     * @throws CbrfException
     */
    public function mkr(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'MKR',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('mkr_base.MKR', $res, Mkr::class);
    }

    /**
     * Returns requirements for credit organisations.
     *
     * @return Dv[]
     *
     * @throws CbrfException
     */
    public function dv(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DV',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('DV_base.DV', $res, Dv::class);
    }

    /**
     * Returns debts of credit organisations.
     *
     * @return RepoDebt[]
     *
     * @throws CbrfException
     */
    public function repoDebt(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Repo_debt',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Repo_debt.RD', $res, RepoDebt::class);
    }

    /**
     * Returns list of Reuters currencies.
     *
     * @return ReutersCurrency[]
     *
     * @throws CbrfException
     */
    public function enumReutersValutes(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'EnumReutersValutes',
            [
                'On_date' => $date,
            ]
        );

        return DataHelper::arrayOfItems('ReutersValutesList.EnumRValutes', $res, ReutersCurrency::class);
    }

    /**
     * Returns list of Reuters rates for all currencies for set date.
     *
     * @return ReutersCurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getReutersCursOnDate(\DateTimeInterface $date): array
    {
        $res = $this->transport->query(
            'GetReutersCursOnDate',
            [
                'On_date' => $date,
            ]
        );

        $results = [];
        $immutableDate = DataHelper::createImmutableDateTime($date);
        foreach (DataHelper::array('ReutersValutesData.Currency', $res) as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }

    /**
     * Returns rates of overnight loans.
     *
     * @return OvernightRate[]
     *
     * @throws CbrfException
     */
    public function overnight(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Overnight',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('Overnight.OB', $res, OvernightRate::class);
    }

    /**
     * Returns rates for currency swap by days.
     *
     * @return SwapDayTotalRate[]
     *
     * @throws CbrfException
     */
    public function swapDayTotal(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapDayTotal',
            [
                'fromDate' => $from,
                'ToDate' => $to,
            ]
        );

        return DataHelper::arrayOfItems('SwapDayTotal.SDT', $res, SwapDayTotalRate::class);
    }
}
