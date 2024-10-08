<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use Liquetsoft\CbrfService\Service\CbrfDailyService;
use Liquetsoft\CbrfService\Transport\CbrfSoapTransport;

/**
 * Factory that can initialize daily service.
 */
final class CbrfFactory
{
    private function __construct()
    {
    }

    /**
     * Creates and returns new CbrfDaily object.
     */
    public static function createDaily(?\SoapClient $soap = null): CbrfDaily
    {
        $transport = new CbrfSoapTransport($soap);

        return new CbrfDailyService($transport);
    }
}
