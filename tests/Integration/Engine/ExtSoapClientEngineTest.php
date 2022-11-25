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
use VCR\VCR;

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
        // @see https://github.com/php-soap/engine-integration-tests/issues/5
        // Currently php-vcr is not in an OK shape. No support for PHP 81 and 82
        // Better to take it out and replace it with something else!
        // In the meantime, we cannot trust these tests.
        return true;
    }

    public function test_it_should_be_possible_to_hook_php_vcr_for_testing()
    {
        static::markTestSkipped('VCR not working properly anymore');
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
