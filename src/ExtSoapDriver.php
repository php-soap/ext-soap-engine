<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\Engine\Decoder;
use Soap\Engine\Driver;
use Soap\Engine\Encoder;
use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\Engine\Metadata\LazyInMemoryMetadata;
use Soap\Engine\Metadata\Metadata;
use Soap\ExtSoapEngine\Generator\DummyMethodArgumentsGenerator;

final class ExtSoapDriver implements Driver
{
    private AbusedClient $client;
    private Encoder $encoder;
    private Decoder $decoder;
    private Metadata $metadata;

    public function __construct(
        AbusedClient $client,
        Encoder $encoder,
        Decoder $decoder,
        Metadata $metadata
    ) {
        $this->client = $client;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->metadata = $metadata;
    }

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        $client = AbusedClient::createFromOptions($options);

        return self::createFromClient(
            $client,
            new LazyInMemoryMetadata(new ExtSoapMetadata($client))
        );
    }

    public static function createFromClient(AbusedClient $client, ?Metadata $metadata = null): self
    {
        $metadata = $metadata ?? new LazyInMemoryMetadata(new ExtSoapMetadata($client));

        return new self(
            $client,
            new ExtSoapEncoder($client),
            new ExtSoapDecoder($client, new DummyMethodArgumentsGenerator($metadata)),
            $metadata
        );
    }

    public function decode(string $method, SoapResponse $response)
    {
        return $this->decoder->decode($method, $response);
    }

    public function encode(string $method, array $arguments): SoapRequest
    {
        return $this->encoder->encode($method, $arguments);
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function getClient(): AbusedClient
    {
        return $this->client;
    }
}
