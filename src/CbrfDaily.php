<?php

namespace marvin255\cbrfservice;

/**
 * Class for a cb RF service.
 */
class CbrfDaily extends BaseServiceSoap
{
    /**
     * @var string
     */
    public $wsdl = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL';

    /**
     * @param string|int $onDate
     *
     * @return array
     */
    public function GetCursOnDate($onDate = null, $currency = null)
    {
        $results = array();
        $onDate = $onDate === null ? time() : $onDate;
        $res = $this->doSoapCall('GetCursOnDate', array(
            array('On_date' => $this->getXsdDateTimeFromDate($onDate)),
        ));
        if (!empty($res->ValuteData->ValuteCursOnDate)) {
            foreach ($res->ValuteData->ValuteCursOnDate as $value) {
                $results[] = array(
                    'VchCode' => (string) $value->VchCode,
                    'Vname' => (string) $value->Vname,
                    'Vcode' => (string) $value->Vcode,
                    'Vcurs' => floatval($value->Vcurs),
                    'Vnom' => floatval($value->Vnom),
                );
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

            return $return;
        } else {
            return $results;
        }
    }

    /**
     * @param bool $seld
     *
     * @return array
     */
    public function EnumValutes($seld = false, $currency = null)
    {
        $results = array();
        $res = $this->doSoapCall('EnumValutes', array(
            array('Seld' => $seld),
        ));
        if (!empty($res->ValuteData->EnumValutes)) {
            foreach ($res->ValuteData->EnumValutes as $value) {
                $results[] = array(
                    'Vcode' => trim($value->Vcode),
                    'Vname' => trim($value->Vname),
                    'VEngname' => trim($value->VEngname),
                    'Vnom' => trim($value->Vnom),
                    'VcommonCode' => trim($value->VcommonCode),
                    'VnumCode' => trim($value->VnumCode),
                    'VcharCode' => trim($value->VcharCode),
                );
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

            return $return;
        } else {
            return $results;
        }
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function GetLatestDateTime($format = 'd.m.Y H:i:s')
    {
        return $this->getTimeMethod('GetLatestDateTime', $format);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function GetLatestDateTimeSeld($format = 'd.m.Y H:i:s')
    {
        return $this->getTimeMethod('GetLatestDateTimeSeld', $format);
    }

    /**
     * @param string $method
     * @param string $format
     *
     * @return string
     */
    protected function getTimeMethod($method, $format = null)
    {
        $return = null;
        $res = $this->doSoapCall($method);
        if (!empty($res)) {
            $return = $format == null ? strtotime($res) : date($format, strtotime($res));
        }

        return $return;
    }

    /**
     * @return string
     */
    public function GetLatestDate()
    {
        return $this->doSoapCall('GetLatestDate');
    }

    /**
     * @return string
     */
    public function GetLatestDateSeld()
    {
        return $this->doSoapCall('GetLatestDateSeld');
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
        $return = array();
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
        $res = $this->doSoapCall('GetCursDynamic', array(array(
            'FromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
            'ValutaCode' => trim($valutaCode),
        )));
        if (!empty($res->ValuteData->ValuteCursDynamic)) {
            foreach ($res->ValuteData->ValuteCursDynamic as $value) {
                $return[] = array(
                    'CursDate' => trim($value->CursDate),
                    'Vcode' => trim($value->Vcode),
                    'Vnom' => (float) $value->Vnom,
                    'Vcurs' => (float) $value->Vcurs,
                );
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
        $result = array();
        $res = $this->doSoapCall('DragMetDynamic', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->DragMetall->DrgMet)) {
            foreach ($res->DragMetall->DrgMet as $value) {
                $result[] = array(
                    'DateMet' => trim($value->DateMet),
                    'CodMet' => trim($value->CodMet),
                    'price' => (float) $value->price,
                );
            }
        }
        if ($code) {
            $return = array();
            foreach ($result as $value) {
                if ($value['CodMet'] == $code) {
                    $return[] = $value;
                }
            }

            return $return;
        } else {
            return $result;
        }
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     *
     * @return array
     */
    public function NewsInfo($fromDate, $toDate)
    {
        $result = array();
        $res = $this->doSoapCall('NewsInfo', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->NewsInfo->News)) {
            foreach ($res->NewsInfo->News as $value) {
                $result[] = array(
                    'Doc_id' => trim($value->Doc_id),
                    'DocDate' => trim($value->DocDate),
                    'Title' => trim($value->Title),
                    'Url' => trim($value->Url),
                );
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
        $result = array();
        $res = $this->doSoapCall('SwapDynamic', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->SwapDynamic->Swap)) {
            foreach ($res->SwapDynamic->Swap as $value) {
                $result[] = array(
                    'DateBuy' => trim($value->DateBuy),
                    'DateSell' => trim($value->DateSell),
                    'BaseRate' => floatval($value->BaseRate),
                    'SD' => floatval($value->SD),
                    'TIR' => floatval($value->TIR),
                    'Stavka' => floatval($value->Stavka),
                    'Currency' => floatval($value->Currency),
                );
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
        $result = array();
        $res = $this->doSoapCall('DepoDynamic', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->DepoDynamic->Depo)) {
            foreach ($res->DepoDynamic->Depo as $value) {
                $result[] = array(
                    'DateDepo' => trim($value->DateDepo),
                    'Overnight' => floatval($value->Overnight),
                    'TomNext' => floatval($value->TomNext),
                    'SpotNext' => floatval($value->SpotNext),
                    'CallDeposit' => floatval($value->CallDeposit),
                );
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
        $result = array();
        $res = $this->doSoapCall('OstatDynamic', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->OstatDynamic->Ostat)) {
            foreach ($res->OstatDynamic->Ostat as $value) {
                $result[] = array(
                    'DateOst' => trim($value->DateOst),
                    'InRuss' => floatval($value->InRuss),
                    'InMoscow' => floatval($value->InMoscow),
                );
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
        $result = array();
        $res = $this->doSoapCall('OstatDepo', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->OD->odr)) {
            foreach ($res->OD->odr as $value) {
                $result[] = array(
                    'D0' => trim($value->D0),
                    'D1_7' => floatval($value->D1_7),
                    'depo' => floatval($value->depo),
                    'total' => floatval($value->total),
                );
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
        $result = array();
        $res = $this->doSoapCall('Saldo', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->Saldo->So)) {
            foreach ($res->Saldo->So as $value) {
                $result[] = array(
                    'Dt' => trim($value->Dt),
                    'DEADLINEBS' => floatval($value->DEADLINEBS),
                );
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
        $result = array();
        $res = $this->doSoapCall('Ruonia', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->Ruonia->ro)) {
            foreach ($res->Ruonia->ro as $value) {
                $result[] = array(
                    'D0' => trim($value->D0),
                    'ruo' => floatval($value->ruo),
                    'vol' => floatval($value->vol),
                );
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
        $result = array();
        $res = $this->doSoapCall('ROISfix', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->ROISfix->rf)) {
            foreach ($res->ROISfix->rf as $value) {
                $result[] = array(
                    'D0' => trim($value->D0),
                    'R1W' => floatval($value->R1W),
                    'R2W' => floatval($value->R2W),
                    'R1M' => floatval($value->R1M),
                    'R2M' => floatval($value->R2M),
                    'R3M' => floatval($value->R3M),
                    'R6M' => floatval($value->R6M),
                );
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
        $result = array();
        $res = $this->doSoapCall('MKR', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->mkr_base->MKR)) {
            foreach ($res->mkr_base->MKR as $value) {
                $result[] = array(
                    'CDate' => trim($value->CDate),
                    'p1' => floatval($value->p1),
                    'd1' => floatval($value->d1),
                    'd7' => floatval($value->d7),
                    'd30' => floatval($value->d30),
                    'd90' => floatval($value->d90),
                    'd180' => floatval($value->d180),
                    'd360' => floatval($value->d360),
                );
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
        $result = array();
        $res = $this->doSoapCall('DV', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->DV_base->DV)) {
            foreach ($res->DV_base->DV as $value) {
                $result[] = array(
                    'Date' => trim($value->Date),
                    'VIDate' => trim($value->VIDate),
                    'VOvern' => floatval($value->VOvern),
                    'VLomb' => floatval($value->VLomb),
                    'VIDay' => floatval($value->VIDay),
                    'VOther' => floatval($value->VOther),
                    'Vol_Gold' => floatval($value->Vol_Gold),
                );
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
        $result = array();
        $res = $this->doSoapCall('Repo_debt', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->Repo_debt->RD)) {
            foreach ($res->Repo_debt->RD as $value) {
                $result[] = array(
                    'Date' => trim($value->Date),
                    'debt' => floatval($value->debt),
                    'debt_fix' => floatval($value->debt_fix),
                );
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
        $result = array();
        $res = $this->doSoapCall('Coins_base', array(array(
            'fromDate' => $this->getXsdDateTimeFromDate($fromDate),
            'ToDate' => $this->getXsdDateTimeFromDate($toDate),
        )));
        if (!empty($res->Coins_base->CB)) {
            foreach ($res->Coins_base->CB as $value) {
                $result[] = array(
                    'date' => trim($value->date),
                    'Cat_number' => trim($value->Cat_number),
                    'name' => trim($value->name),
                    'Metall' => trim($value->Metall),
                    'nominal' => floatval($value->nominal),
                    'Q' => floatval($value->Q),
                    'PriceBR' => floatval($value->PriceBR),
                );
            }
        }

        return $result;
    }

    /**
     * @param mixed $result
     *
     * @return mixed
     */
    protected function parseSoapResult($result, $method, $params)
    {
        $resName = $method . 'Result';
        if (!empty($result->$resName->any)) {
            return simplexml_load_string($result->$resName->any);
        } elseif (!empty($result->$resName)) {
            return $result->$resName;
        } else {
            return null;
        }
    }
}
