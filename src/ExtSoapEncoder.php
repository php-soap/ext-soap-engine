<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\Engine\Encoder;
use Soap\Engine\HttpBinding\SoapRequest;

final class ExtSoapEncoder implements Encoder
{
    private AbusedClient $client;

    public function __construct(AbusedClient $client)
    {
        $this->client = $client;
    }

    public function encode(string $method, array $arguments): SoapRequest
    {
        try {
            $this->client->__soapCall($method, $arguments);
            $encoded = $this->client->collectRequest();
        } finally {
            $this->client->cleanUpTemporaryState();
        }

        return $encoded;
    }
}
