<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration;

use Soap\Engine\Decoder;
use Soap\EngineIntegrationTests\AbstractDecoderTest;
use Soap\EngineIntegrationTests\Type\ValidateResponse;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\ExtSoapDecoder;
use Soap\ExtSoapEngine\ExtSoapMetadata;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Generator\DummyMethodArgumentsGenerator;

class ExtSoapDecoderTest extends AbstractDecoderTest
{
    private ExtSoapDecoder $decoder;

    protected function getDecoder(): Decoder
    {
        return $this->decoder;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->decoder = new ExtSoapDecoder(
            $client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl, [])
                    ->disableWsdlCache()
                    ->withClassMap(new ClassMapCollection(
                        new ClassMap('MappedValidateResponse', ValidateResponse::class),
                    ))
            ),
            new DummyMethodArgumentsGenerator(new ExtSoapMetadata($client))
        );
    }
}
