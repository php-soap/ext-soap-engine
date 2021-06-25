<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration;

use Soap\Engine\Encoder;
use Soap\EngineIntegrationTests\AbstractEncoderTest;
use Soap\EngineIntegrationTests\Type\ValidateRequest;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\ExtSoapEncoder;
use Soap\ExtSoapEngine\ExtSoapOptions;

class ExtSoapEncoderTest extends AbstractEncoderTest
{
    private ExtSoapEncoder $encoder;

    protected function getEncoder(): Encoder
    {
        return $this->encoder;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->encoder = new ExtSoapEncoder(
            $client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl)
                    ->disableWsdlCache()
                    ->withClassMap(new ClassMapCollection(
                        new ClassMap('MappedValidateRequest', ValidateRequest::class),
                    ))
            )
        );
    }
}
