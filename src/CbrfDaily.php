<?php

declare(strict_types=1);

namespace Marvin255\CbrfService;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin255\CbrfService\Entity\Currency;
use Marvin255\CbrfService\Entity\CurrencyRate;
use Marvin255\CbrfService\Entity\ReutersCurrencyRate;
use SoapClient;
use Throwable;

/**
 * Class for a daily cb RF service.
 */
class CbrfDaily
{
    private CbrfSoapService $soapClient;

    /**
     * @param SoapClient|string $client
     */
    public function __construct($client = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL')
    {
        $this->soapClient = new CbrfSoapService($client);
    }

    /**
     * Returns list of rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return CurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getCursOnDate(DateTimeInterface $date): array
    {
        $soapResult = $this->soapClient->query(
            'GetCursOnDate',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        $results = [];
        $immutableDate = new DateTimeImmutable($date->format(\DATE_ATOM));

        $currencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($currencyRateList as $item) {
            if (\is_array($item)) {
                $results[] = new CurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }

    /**
     * Returns list of rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return CurrencyRate|null
     *
     * @throws CbrfException
     */
    public function getCursOnDateByCode(DateTimeInterface $date, string $code): ?CurrencyRate
    {
        $currencyItem = null;
        $code = strtoupper(trim($code));

        $soapResult = $this->soapClient->query(
            'GetCursOnDate',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        // looks like repeating of getCurrencyRate
        // but we do not want to instantiate objects for all currencies
        $currencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($currencyRateList as $item) {
            $itemCode = strtoupper(trim($item['VchCode'] ?? ''));
            if ($code === $itemCode) {
                $immutableDate = new DateTimeImmutable($date->format(\DATE_ATOM));
                $currencyItem = new CurrencyRate($item, $immutableDate);
                break;
            }
        }

        return $currencyItem;
    }

    /**
     * List of all currencies that allowed on cbrf service.
     *
     * @param bool $seld
     *
     * @return Currency[]
     *
     * @throws CbrfException
     */
    public function enumValutes(bool $seld = false): array
    {
        $soapResult = $this->soapClient->query(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        $results = [];

        $enumValutes = $soapResult['ValuteData']['EnumValutes'] ?? [];
        foreach ($enumValutes as $item) {
            if (\is_array($item)) {
                $results[] = new Currency($item);
            }
        }

        return $results;
    }

    /**
     * Latest per day date and time of publication.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateTime(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateTime');

        return $this->createDateFromString($soapResult['GetLatestDateTimeResult'] ?? '');
    }

    /**
     * Latest per day date and time of seld.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateTimeSeld(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateTimeSeld');

        return $this->createDateFromString($soapResult['GetLatestDateTimeSeldResult'] ?? '');
    }

    /**
     * Latest per month date and time of publication.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDate(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDate');

        return $this->createDateFromString($soapResult['GetLatestDateResult'] ?? '');
    }

    /**
     * Latest per month date and time of seld.
     *
     * @param string $format
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    public function getLatestDateSeld(): DateTimeInterface
    {
        $soapResult = $this->soapClient->query('GetLatestDateSeld');

        return $this->createDateFromString($soapResult['GetLatestDateSeldResult'] ?? '');
    }

    /**
     * Returns list of Reuters rates for all currencies for set date.
     *
     * @param DateTimeInterface $onDate
     *
     * @return ReutersCurrencyRate[]
     *
     * @throws CbrfException
     */
    public function getReutersCursOnDate(DateTimeInterface $date): array
    {
        $enumSoapResults = $this->soapClient->query(
            'EnumReutersValutes',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        $enumCurrencies = [];
        foreach ($enumSoapResults['ReutersValutesList']['EnumRValutes'] as $enumSoapResult) {
            $enumCurrencies[$enumSoapResult['num_code']] = $enumSoapResult;
        }

        $soapValutesResults = $this->soapClient->query(
            'GetReutersCursOnDate',
            [
                'On_date' => $date->format('Y-m-d\TH:i:s'),
            ]
        );

        foreach ($soapValutesResults['ReutersValutesData']['Currency'] as $soapValutesResult) {
            $enumCurrencies[$soapValutesResult['num_code']] = array_merge($enumCurrencies[$soapValutesResult['num_code']], $soapValutesResult);
        }

        $results = [];
        $immutableDate = new DateTimeImmutable($date->format(\DATE_ATOM));

        foreach ($enumCurrencies as $item) {
            if (\is_array($item)) {
                $results[] = new ReutersCurrencyRate($item, $immutableDate);
            }
        }

        return $results;
    }

    /**
     * Creates DateTimeInterface object from set string.
     *
     * @param string $date
     *
     * @return DateTimeInterface
     *
     * @throws CbrfException
     */
    private function createDateFromString(string $date): DateTimeInterface
    {
        try {
            return new DateTimeImmutable($date);
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }
    }
}
