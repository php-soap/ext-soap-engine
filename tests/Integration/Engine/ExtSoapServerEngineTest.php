<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration\Engine;

use Soap\Engine\Engine;
use Soap\Engine\SimpleEngine;
use Soap\Engine\Transport;
use Soap\EngineIntegrationTests\AbstractEngineTest;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Transport\ExtSoapServerTransport;

class ExtSoapServerEngineTest extends AbstractEngineTest
{
    private Engine $engine;

    protected function getEngine(): Engine
    {
        return $this->engine;
    }

    protected function getVcrPrefix(): string
    {
        return 'ext-soap-with-server-handle-';
    }

    protected function skipVcr(): bool
    {
        return true;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->engine = new SimpleEngine(
            ExtSoapDriver::createFromClient(
                $client = AbusedClient::createFromOptions(
                    ExtSoapOptions::defaults($wsdl, [
                        'cache_wsdl' => WSDL_CACHE_NONE,
                        'soap_version' => SOAP_1_2,
                    ])
                )
            ),
            new ExtSoapServerTransport($this->mockServerForWsdl($wsdl))
        );
    }

    private function mockServerForWsdl(string $wsdl): \SoapServer
    {
        $server = new \SoapServer($wsdl, ['soap_version' => SOAP_1_2]);
        $server->setObject(new class() {
            public function GetCityWeatherByZIP($zip) {
                return [
                    'GetCityWeatherByZIPResult' => [
                        'WeatherID' => 1,
                        'Success' => true,
                    ]
                ];
            }
        });

        return $server;
    }
}
