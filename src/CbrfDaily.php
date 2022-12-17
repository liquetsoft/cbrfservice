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
use Liquetsoft\CbrfService\Entity\PreciousMetalRate;
use Liquetsoft\CbrfService\Entity\RepoDebt;
use Liquetsoft\CbrfService\Entity\ReutersCurrencyRate;
use Liquetsoft\CbrfService\Entity\RuoniaBid;
use Liquetsoft\CbrfService\Entity\RuoniaIndex;
use Liquetsoft\CbrfService\Entity\Saldo;
use Liquetsoft\CbrfService\Entity\SwapRate;

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
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
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
     * @return CurrencyRate|null
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
     * @return CurrencyRate|null
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
     * @return CurrencyEnum|null
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
     * @param int  $numericCode
     * @param bool $seld
     *
     * @return CurrencyEnum|null
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
     * @param string $format
     *
     * @return \DateTimeInterface
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
     * @param string $format
     *
     * @return \DateTimeInterface
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
     * @param string $format
     *
     * @return \DateTimeInterface
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
     * @param string $format
     *
     * @return \DateTimeInterface
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
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     * @param CurrencyEnum       $currency
     *
     * @return CurrencyRate[]
     */
    public function getCursDynamic(\DateTimeInterface $from, \DateTimeInterface $to, CurrencyEnum $currency): array
    {
        $res = $this->transport->query(
            'GetCursDynamic',
            [
                'FromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ValutaCode' => $currency->getInternalCode(),
            ]
        );

        $result = [];
        $list = DataHelper::array('ValuteData.ValuteCursDynamic', $res);
        foreach ($list as $item) {
            $date = DataHelper::dateTime('CursDate', $item);
            $item['Vname'] = $currency->getName();
            $item['VchCode'] = $currency->getCharCode();
            $item['Vcode'] = $currency->getNumericCode();
            $result[] = new CurrencyRate($item, $date);
        }

        return $result;
    }

    /**
     * Returns key rate dynamic within set dates.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return KeyRate[]
     */
    public function keyRate(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'KeyRate',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('KeyRate.KR', $res, KeyRate::class);
    }

    /**
     * Returns list of presious metals prices within set dates.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return PreciousMetalRate[]
     */
    public function dragMetDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DragMetDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('DragMetall.DrgMet', $res, PreciousMetalRate::class);
    }

    /**
     * Returns list of swap rates within set dates.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return SwapRate[]
     */
    public function swapDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'SwapDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('SwapDynamic.Swap', $res, SwapRate::class);
    }

    /**
     * Returns list depo dynamic items within set dates.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return DepoRate[]
     */
    public function depoDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DepoDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('DepoDynamic.Depo', $res, DepoRate::class);
    }

    /**
     * Returns the dynamic of balances of funds items within set dates.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return OstatRate[]
     */
    public function ostatDynamic(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDynamic',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('OstatDynamic.Ostat', $res, OstatRate::class);
    }

    /**
     * Returns the banks deposites at bank of Russia.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return OstatDepoRate[]
     */
    public function ostatDepo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'OstatDepo',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('OD.odr', $res, OstatDepoRate::class);
    }

    /**
     * Returns international valute reseves of Russia for month.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return InternationalReserve[]
     */
    public function mrrf(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('mmrf.mr', $res, InternationalReserve::class);
    }

    /**
     * Returns international valute reseves of Russia for week.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return InternationalReserveWeek[]
     */
    public function mrrf7d(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'mrrf7D',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('mmrf7d.mr', $res, InternationalReserveWeek::class);
    }

    /**
     * Returns operations saldo.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return Saldo[]
     */
    public function saldo(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Saldo',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('Saldo.So', $res, Saldo::class);
    }

    /**
     * Returns Ruonia index.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return RuoniaIndex[]
     */
    public function ruoniaSV(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'RuoniaSV',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('RuoniaSV.ra', $res, RuoniaIndex::class);
    }

    /**
     * Returns Ruonia bid.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return RuoniaBid[]
     */
    public function ruonia(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Ruonia',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('Ruonia.ro', $res, RuoniaBid::class);
    }

    /**
     * Returns inter banks credit market bids.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return Mkr[]
     */
    public function mkr(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'MKR',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('mkr_base.MKR', $res, Mkr::class);
    }

    /**
     * Returns requirements for credit organisations.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return Dv[]
     */
    public function dv(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'DV',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('DV_base.DV', $res, Dv::class);
    }

    /**
     * Returns debts of credit organisations.
     *
     * @param \DateTimeInterface $from
     * @param \DateTimeInterface $to
     *
     * @return RepoDebt[]
     */
    public function repoDebt(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $res = $this->transport->query(
            'Repo_debt',
            [
                'fromDate' => $from->format(CbrfSoapService::DATE_TIME_FORMAT),
                'ToDate' => $to->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        return DataHelper::arrayOfItems('Repo_debt.RD', $res, RepoDebt::class);
    }

    /**
     * Returns list of Reuters rates for all currencies for set date.
     *
     * @param \DateTimeInterface $onDate
     *
     * @return ReutersCurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getReutersCursOnDate(\DateTimeInterface $date): array
    {
        $enumSoapResults = $this->transport->query(
            'EnumReutersValutes',
            [
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        $enumCurrencies = [];
        foreach ($enumSoapResults['ReutersValutesList']['EnumRValutes'] as $enumSoapResult) {
            $enumCurrencies[$enumSoapResult['num_code']] = $enumSoapResult;
        }

        $soapValutesResults = $this->transport->query(
            'GetReutersCursOnDate',
            [
                'On_date' => $date->format(CbrfSoapService::DATE_TIME_FORMAT),
            ]
        );

        foreach ($soapValutesResults['ReutersValutesData']['Currency'] as $soapValutesResult) {
            $enumCurrencies[$soapValutesResult['num_code']] = array_merge($enumCurrencies[$soapValutesResult['num_code']], $soapValutesResult);
        }

        $results = [];
        $immutableDate = DataHelper::createImmutableDateTime($date);

        foreach ($enumCurrencies as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }
}
