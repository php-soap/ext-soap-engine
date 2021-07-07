<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration\Engine;

use Soap\Engine\Engine;
use Soap\Engine\SimpleEngine;
use Soap\EngineIntegrationTests\AbstractEngineTest;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Transport\ExtSoapClientTransport;

final class ExtSoapClientEngineTest extends AbstractEngineTest
{
    private Engine $engine;

    protected function getEngine(): Engine
    {
        return $this->engine;
    }

    protected function getVcrPrefix(): string
    {
        return 'ext-soap-with-client-handle-';
    }

    protected function skipVcr(): bool
    {
        return false;
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
            new ExtSoapClientTransport($client)
        );
    }
}
