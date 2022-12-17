<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use Liquetsoft\CbrfService\Exception\CbrfException;
use SoapClient;

/**
 * Class for cbrf SOAP service.
 */
final class CbrfSoapTransport implements CbrfTransport
{
    public const DEFAULT_WSDL = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL';
    private const DATE_TIME_FORMAT = 'Y-m-d\TH:i:s';

    private ?\SoapClient $client = null;

    public function __construct(?\SoapClient $client = null)
    {
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $method, ?array $params = null): array
    {
        try {
            return $this->queryInternal($method, $params ?: []);
        } catch (\Throwable $e) {
            $message = sprintf("Fail on '%s': '%s'.", $method, $e->getMessage());
            throw new CbrfException($message, 0, $e);
        }
    }

    /**
     * Makes an internal soap call.
     *
     * @param string $method
     * @param array  $params
     *
     * @return array
     */
    private function queryInternal(string $method, array $params = []): array
    {
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
                \LIBXML_NOCDATA
            );
            $parsedResult = $this->xml2array($xml);
        } else {
            $parsedResult = (array) $soapCallResult;
        }

        return $parsedResult;
    }

    /**
     * Converts SimpleXMLElement to an associative array.
     */
    private function xml2array(mixed $xmlObject): array
    {
        $out = [];

        $xmlArray = (array) $xmlObject;
        foreach ($xmlArray as $index => $node) {
            if (\is_object($node) || \is_array($node)) {
                $out[$index] = $this->xml2array($node);
            } else {
                $out[$index] = $node;
            }
        }

        return $out;
    }

    /**
     * Returns a SoapClient instance for soap requests.
     */
    private function getSoapClient(): \SoapClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $this->client = new \SoapClient(
            self::DEFAULT_WSDL,
            [
                'exception' => true,
            ]
        );

        return $this->client;
    }
}
