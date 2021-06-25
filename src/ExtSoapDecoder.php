<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\Engine\Decoder;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\ExtSoapEngine\Generator\DummyMethodArgumentsGenerator;

final class ExtSoapDecoder implements Decoder
{
    private AbusedClient $client;
    private DummyMethodArgumentsGenerator $argumentsGenerator;

    public function __construct(AbusedClient $client, DummyMethodArgumentsGenerator $argumentsGenerator)
    {
        $this->client = $client;
        $this->argumentsGenerator = $argumentsGenerator;
    }

    public function decode(string $method, SoapResponse $response)
    {
        $this->client->registerResponse($response);
        try {
            $decoded = $this->client->__soapCall($method, $this->argumentsGenerator->generateForSoapCall($method));
        } finally {
            $this->client->cleanUpTemporaryState();
        }
        return $decoded;
    }
}
