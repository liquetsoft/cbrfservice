<?php

namespace Marvin255\CbrfService;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin255\CbrfService\Entity\Currency;
use Marvin255\CbrfService\Entity\CurrencyRate;
use SoapClient;
use Throwable;

/**
 * Class for a daily cb RF service.
 */
class CbrfDaily
{
    private ?string $wsdl = null;

    private ?SoapClient $client = null;

    /**
     * @param SoapClient|string $client
     */
    public function __construct($client = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL')
    {
        if ($client instanceof SoapClient) {
            $this->client = $client;
        } else {
            $this->wsdl = $client;
        }
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
        $soapResult = $this->doSoapCall(
            'GetCursOnDate',
            [
                'On_date' => $this->convertDateToXsdFormat($date),
            ]
        );

        $results = [];
        $immutableDate = new DateTimeImmutable($date->format(DATE_ATOM));

        $CurrencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($CurrencyRateList as $item) {
            if (is_array($item)) {
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

        $soapResult = $this->doSoapCall(
            'GetCursOnDate',
            [
                'On_date' => $this->convertDateToXsdFormat($date),
            ]
        );

        // looks like repeating of getCurrencyRate
        // but we do not want to instantiate objects for all currencies
        $CurrencyRateList = $soapResult['ValuteData']['ValuteCursOnDate'] ?? [];
        foreach ($CurrencyRateList as $item) {
            $itemCode = strtoupper(trim($item['VchCode'] ?? ''));
            if ($code === $itemCode) {
                $immutableDate = new DateTimeImmutable($date->format(DATE_ATOM));
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
     */
    public function enumValutes(bool $seld = false): array
    {
        $soapResult = $this->doSoapCall(
            'EnumValutes',
            [
                'Seld' => $seld,
            ]
        );

        $results = [];

        $enumValutes = $soapResult['ValuteData']['EnumValutes'] ?? [];
        foreach ($enumValutes as $item) {
            if (is_array($item)) {
                $results[] = new Currency($item);
            }
        }

        return $results;
    }

    // /**
    //  * @param string $format
    //  *
    //  * @return int|string|null
    //  */
    // public function GetLatestDateTime($format = 'd.m.Y H:i:s')
    // {
    //     return $this->getTimeMethod('GetLatestDateTime', $format);
    // }
    //
    // /**
    //  * @param string $format
    //  *
    //  * @return int|string|null
    //  */
    // public function GetLatestDateTimeSeld($format = 'd.m.Y H:i:s')
    // {
    //     return $this->getTimeMethod('GetLatestDateTimeSeld', $format);
    // }
    //
    // /**
    //  * @param string $format
    //  *
    //  * @return string|int|null
    //  */
    // public function GetLatestDate($format = 'Ymd')
    // {
    //     $return = null;
    //     if ($res = $this->doSoapCall('GetLatestDate')) {
    //         $timestamp = strtotime(
    //             substr($res, 0, 4) . '-' . substr($res, 4, 2) . '-' . substr($res, 6, 2)
    //         );
    //         if ($timestamp !== false) {
    //             $return = $format ? date($format, $timestamp) : $timestamp;
    //         }
    //     }
    //
    //     return $return;
    // }
    //
    // /**
    //  * @param string $format
    //  *
    //  * @return string|int|null
    //  */
    // public function GetLatestDateSeld($format = 'Ymd')
    // {
    //     $return = null;
    //     if ($res = $this->doSoapCall('GetLatestDateSeld')) {
    //         $timestamp = strtotime(
    //             substr($res, 0, 4) . '-' . substr($res, 4, 2) . '-' . substr($res, 6, 2)
    //         );
    //         if ($timestamp !== false) {
    //             $return = $format ? date($format, $timestamp) : $timestamp;
    //         }
    //     }
    //
    //     return $return;
    // }
    //
    // /**
    //  * @param string $fromDate
    //  * @param string $toDate
    //  * @param string $valutaCode
    //  * @param bool   $findCode
    //  *
    //  * @return array
    //  */
    // public function GetCursDynamic($fromDate, $toDate, $valutaCode, $findCode = false)
    // {
    //     $return = [];
    //
    //     if ($findCode) {
    //         $valute = $this->EnumValutes(false, $valutaCode);
    //         if (!$valute) {
    //             $valute = $this->EnumValutes(true, $valutaCode);
    //         }
    //         if (!empty($valute['Vcode'])) {
    //             $valutaCode = $valute['Vcode'];
    //         } else {
    //             return $return;
    //         }
    //     }
    //
    //     $res = $this->doSoapCall('GetCursDynamic', [[
    //         'FromDate' => $this->getXsdDateTimeFromDate($fromDate),
    //         'ToDate' => $this->getXsdDateTimeFromDate($toDate),
    //         'ValutaCode' => trim($valutaCode),
    //     ]]);
    //
    //     if (!empty($res->ValuteData->ValuteCursDynamic)) {
    //         foreach ($res->ValuteData->ValuteCursDynamic as $value) {
    //             $return[] = [
    //                 'CursDate' => trim($value->CursDate),
    //                 'Vcode' => trim($value->Vcode),
    //                 'Vnom' => floatval($value->Vnom),
    //                 'Vcurs' => floatval($value->Vcurs),
    //             ];
    //         }
    //     }
    //
    //     return $return;
    // }
    //
    // /**
    //  * @param string $method
    //  * @param string $format
    //  *
    //  * @return string|int|null
    //  */
    // protected function getTimeMethod($method, $format = null)
    // {
    //     $return = null;
    //     $res = $this->doSoapCall($method);
    //     if (!empty($res)) {
    //         $strtotime = strtotime($res);
    //         if ($strtotime !== false) {
    //             $return = $format === null ? $strtotime : date($format, $strtotime);
    //         }
    //     }
    //
    //     return $return;
    // }

    /**
     * Converts any date to xsd DateTime format string.
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    private function convertDateToXsdFormat(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:s');
    }

    /**
     * Makes a soap call.
     *
     * @param string $method
     * @param array  $params
     *
     * @return array
     *
     * @throws CbrfException
     */
    private function doSoapCall(string $method, array $params = []): array
    {
        $parsedResult = [];

        try {
            // need to do this because every params list are nested to parameters object
            if (!empty($params)) {
                $params = [$params];
            }
            $soapCallResult = $this->getSoapClient()->__soapCall($method, $params);

            $resName = $method . 'Result';
            if (!empty($soapCallResult->$resName->any)) {
                $xml = simplexml_load_string(
                    $soapCallResult->$resName->any,
                    'SimpleXMLElement',
                    LIBXML_NOCDATA
                );
                $parsedResult = $this->xml2array($xml);
            }
        } catch (Throwable $e) {
            $message = sprintf("Fail on '%s': '%s'.", $method, $e->getMessage());
            throw new CbrfException($message, 0, $e);
        }

        return $parsedResult;
    }

    /**
     * Converts SimpleXMLElement to an associative array,.
     *
     * @param mixed $xmlObject
     *
     * @return array<string, mixed>
     */
    private function xml2array($xmlObject): array
    {
        $out = [];

        $xmlArray = (array) $xmlObject;
        foreach ($xmlArray as $index => $node) {
            if (is_object($node) || is_array($node)) {
                $out[$index] = $this->xml2array($node);
            } else {
                $out[$index] = $node;
            }
        }

        return $out;
    }

    /**
     * Returns a SoapClient instance for soap requests.
     *
     * @return SoapClient
     *
     * @throws CbrfException
     */
    private function getSoapClient()
    {
        if ($this->client !== null) {
            return $this->client;
        }

        if ($this->wsdl === null) {
            $message = sprintf("Provided client must be string with valid url to WSDL file or a '%s' instance.", SoapClient::class);
            throw new CbrfException($message);
        }

        try {
            $this->client = new SoapClient(
                $this->wsdl,
                [
                    'exception' => true,
                ]
            );
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $this->client;
    }
}
