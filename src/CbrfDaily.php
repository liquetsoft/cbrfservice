<?php

namespace Marvin255\CbrfService;

use SoapClient;

/**
 * Class for a daily cb RF service.
 */
class CbrfDaily extends SoapService
{
    /**
     * {@inheritdoc}
     */
    public function __construct(SoapClient $client = null)
    {
        if (!$client) {
            $client = new SoapClient(
                'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL',
                [
                    'exception' => true,
                ]
            );
        }

        return parent::__construct($client);
    }

    /**
     * @param string|int $onDate
     * @param string     $currency
     *
     * @return array|null
     */
    public function GetCursOnDate($onDate = null, $currency = null)
    {
        $results = [];
        $onDate = $onDate === null ? time() : $onDate;

        $res = $this->doSoapCall('GetCursOnDate', [
            ['On_date' => $this->getXsdDateTimeFromDate($onDate)],
        ]);

        if (!empty($res->ValuteData->ValuteCursOnDate)) {
            foreach ($res->ValuteData->ValuteCursOnDate as $value) {
                $results[] = [
                    'VchCode' => trim($value->VchCode),
                    'Vname' => trim($value->Vname),
                    'Vcode' => trim($value->Vcode),
                    'Vcurs' => floatval($value->Vcurs),
                    'Vnom' => floatval($value->Vnom),
                ];
            }
        }

        if ($currency !== null) {
            $return = null;
            foreach ($results as $value) {
                if ($value['VchCode'] == $currency || $value['Vname'] == $currency || $value['Vcode'] == $currency) {
                    $return = $value;
                    break;
                }
            }
            $results = $return;
        }

        return $results;
    }

    /**
     * @param bool   $seld
     * @param string $currency
     *
     * @return array|null
     */
    public function EnumValutes($seld = false, $currency = null)
    {
        $results = [];

        $res = $this->doSoapCall('EnumValutes', [
            ['Seld' => $seld],
        ]);

        if (!empty($res->ValuteData->EnumValutes)) {
            foreach ($res->ValuteData->EnumValutes as $value) {
                $results[] = [
                    'Vcode' => trim($value->Vcode),
                    'Vname' => trim($value->Vname),
                    'VEngname' => trim($value->VEngname),
                    'Vnom' => trim($value->Vnom),
                    'VcommonCode' => trim($value->VcommonCode),
                    'VnumCode' => trim($value->VnumCode),
                    'VcharCode' => trim($value->VcharCode),
                ];
            }
        }

        if ($currency !== null) {
            $return = null;
            foreach ($results as $value) {
                if ($value['VcommonCode'] == $currency || $value['VcharCode'] == $currency || $value['Vname'] == $currency || $value['Vcode'] == $currency) {
                    $return = $value;
                    break;
                }
            }
            $results = $return;
        }

        return $results;
    }

    /**
     * @param string $format
     *
     * @return int|string|null
     */
    public function GetLatestDateTime($format = 'd.m.Y H:i:s')
    {
        return $this->getTimeMethod('GetLatestDateTime', $format);
    }

    /**
     * @param string $format
     *
     * @return int|string|null
     */
    public function GetLatestDateTimeSeld($format = 'd.m.Y H:i:s')
    {
        return $this->getTimeMethod('GetLatestDateTimeSeld', $format);
    }

    /**
     * @param string $format
     *
     * @return string|int|null
     */
    public function GetLatestDate($format = 'Ymd')
    {
        $return = null;
        if ($res = $this->doSoapCall('GetLatestDate')) {
            $timestamp = strtotime(
                substr($res, 0, 4) . '-' . substr($res, 4, 2) . '-' . substr($res, 6, 2)
            );
            if ($timestamp !== false) {
                $return = $format ? date($format, $timestamp) : $timestamp;
            }
        }

        return $return;
    }

    /**
     * @param string $format
     *
     * @return string|int|null
     */
    public function GetLatestDateSeld($format = 'Ymd')
    {
        $return = null;
        if ($res = $this->doSoapCall('GetLatestDateSeld')) {
            $timestamp = strtotime(
                substr($res, 0, 4) . '-' . substr($res, 4, 2) . '-' . substr($res, 6, 2)
            );
            if ($timestamp !== false) {
                $return = $format ? date($format, $timestamp) : $timestamp;
            }
        }

        return $return;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string $valutaCode
     * @param bool   $findCode
     *
     * @return array
     */
    public function GetCursDynamic($fromDate, $toDate, $valutaCode, $findCode = false)
    {
        $return = [];

        if ($findCode) {
            $valute = $this->EnumValutes(false, $valutaCode);
            if (!$valute) {
                $valute = $this->EnumValutes(true, $valutaCode);
            }
            if (!empty($valute['Vcode'])) {
                $valutaCode = $valute['Vcode'];
            } else {
                return $return;
            }
        }

        $res = $this->doSoapCall('GetCursDynamic', [[
            'FromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
            'ValutaCode' => trim($valutaCode),
        ]]);

        if (!empty($res->ValuteData->ValuteCursDynamic)) {
            foreach ($res->ValuteData->ValuteCursDynamic as $value) {
                $return[] = [
                    'CursDate' => trim($value->CursDate),
                    'Vcode' => trim($value->Vcode),
                    'Vnom' => floatval($value->Vnom),
                    'Vcurs' => floatval($value->Vcurs),
                ];
            }
        }

        return $return;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string $code
     *
     * @return array
     */
    public function DragMetDynamic($fromDate, $toDate, $code = null)
    {
        $result = [];
        $res = $this->doSoapCall('DragMetDynamic', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->DragMetall->DrgMet)) {
            foreach ($res->DragMetall->DrgMet as $value) {
                $result[] = [
                    'DateMet' => trim($value->DateMet),
                    'CodMet' => trim($value->CodMet),
                    'price' => floatval($value->price),
                ];
            }
        }

        if ($code) {
            $return = [];
            foreach ($result as $value) {
                if ($value['CodMet'] == $code) {
                    $return[] = $value;
                }
            }
            $result = $return;
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function NewsInfo($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('NewsInfo', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->NewsInfo->News)) {
            foreach ($res->NewsInfo->News as $value) {
                $result[] = [
                    'Doc_id' => trim($value->Doc_id),
                    'DocDate' => trim($value->DocDate),
                    'Title' => trim($value->Title),
                    'Url' => trim($value->Url),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function SwapDynamic($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('SwapDynamic', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->SwapDynamic->Swap)) {
            foreach ($res->SwapDynamic->Swap as $value) {
                $result[] = [
                    'DateBuy' => trim($value->DateBuy),
                    'DateSell' => trim($value->DateSell),
                    'BaseRate' => floatval($value->BaseRate),
                    'SD' => floatval($value->SD),
                    'TIR' => floatval($value->TIR),
                    'Stavka' => floatval($value->Stavka),
                    'Currency' => floatval($value->Currency),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function DepoDynamic($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('DepoDynamic', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->DepoDynamic->Depo)) {
            foreach ($res->DepoDynamic->Depo as $value) {
                $result[] = [
                    'DateDepo' => trim($value->DateDepo),
                    'Overnight' => floatval($value->Overnight),
                    'TomNext' => floatval($value->TomNext),
                    'SpotNext' => floatval($value->SpotNext),
                    'CallDeposit' => floatval($value->CallDeposit),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function OstatDynamic($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('OstatDynamic', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->OstatDynamic->Ostat)) {
            foreach ($res->OstatDynamic->Ostat as $value) {
                $result[] = [
                    'DateOst' => trim($value->DateOst),
                    'InRuss' => floatval($value->InRuss),
                    'InMoscow' => floatval($value->InMoscow),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function OstatDepo($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('OstatDepo', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->OD->odr)) {
            foreach ($res->OD->odr as $value) {
                $result[] = [
                    'D0' => trim($value->D0),
                    'D1_7' => floatval($value->D1_7),
                    'depo' => floatval($value->depo),
                    'total' => floatval($value->total),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function Saldo($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('Saldo', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->Saldo->So)) {
            foreach ($res->Saldo->So as $value) {
                $result[] = [
                    'Dt' => trim($value->Dt),
                    'DEADLINEBS' => floatval($value->DEADLINEBS),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function Ruonia($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('Ruonia', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->Ruonia->ro)) {
            foreach ($res->Ruonia->ro as $value) {
                $result[] = [
                    'D0' => trim($value->D0),
                    'ruo' => floatval($value->ruo),
                    'vol' => floatval($value->vol),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function ROISfix($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('ROISfix', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->ROISfix->rf)) {
            foreach ($res->ROISfix->rf as $value) {
                $result[] = [
                    'D0' => trim($value->D0),
                    'R1W' => floatval($value->R1W),
                    'R2W' => floatval($value->R2W),
                    'R1M' => floatval($value->R1M),
                    'R2M' => floatval($value->R2M),
                    'R3M' => floatval($value->R3M),
                    'R6M' => floatval($value->R6M),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function MKR($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('MKR', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->mkr_base->MKR)) {
            foreach ($res->mkr_base->MKR as $value) {
                $result[] = [
                    'CDate' => trim($value->CDate),
                    'p1' => floatval($value->p1),
                    'd1' => floatval($value->d1),
                    'd7' => floatval($value->d7),
                    'd30' => floatval($value->d30),
                    'd90' => floatval($value->d90),
                    'd180' => floatval($value->d180),
                    'd360' => floatval($value->d360),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function DV($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('DV', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->DV_base->DV)) {
            foreach ($res->DV_base->DV as $value) {
                $result[] = [
                    'Date' => trim($value->Date),
                    'VIDate' => trim($value->VIDate),
                    'VOvern' => floatval($value->VOvern),
                    'VLomb' => floatval($value->VLomb),
                    'VIDay' => floatval($value->VIDay),
                    'VOther' => floatval($value->VOther),
                    'Vol_Gold' => floatval($value->Vol_Gold),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function Repo_debt($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('Repo_debt', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->Repo_debt->RD)) {
            foreach ($res->Repo_debt->RD as $value) {
                $result[] = [
                    'Date' => trim($value->Date),
                    'debt' => floatval($value->debt),
                    'debt_fix' => floatval($value->debt_fix),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function Coins_base($fromDate, $toDate)
    {
        $result = [];

        $res = $this->doSoapCall('Coins_base', [[
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        ]]);

        if (!empty($res->Coins_base->CB)) {
            foreach ($res->Coins_base->CB as $value) {
                $result[] = [
                    'date' => trim($value->date),
                    'Cat_number' => trim($value->Cat_number),
                    'name' => trim($value->name),
                    'Metall' => trim($value->Metall),
                    'nominal' => floatval($value->nominal),
                    'Q' => floatval($value->Q),
                    'PriceBR' => floatval($value->PriceBR),
                ];
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseSoapResult($result, $method, $params)
    {
        $return = null;
        $resName = $method . 'Result';

        if (!empty($result->$resName->any)) {
            $return = simplexml_load_string($result->$resName->any);
        } elseif (!empty($result->$resName)) {
            $return = $result->$resName;
        }

        return $return;
    }

    /**
     * @param string $method
     * @param string $format
     *
     * @return string|int|null
     */
    protected function getTimeMethod($method, $format = null)
    {
        $return = null;
        $res = $this->doSoapCall($method);
        if (!empty($res)) {
            $strtotime = strtotime($res);
            if ($strtotime !== false) {
                $return = $format === null ? $strtotime : date($format, $strtotime);
            }
        }

        return $return;
    }
}
