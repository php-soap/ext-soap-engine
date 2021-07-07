<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration;

use Soap\Engine\Metadata\MetadataProvider;
use Soap\EngineIntegrationTests\AbstractMetadataProviderTest;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;

final class ExtSoapMetadataProviderTest extends AbstractMetadataProviderTest
{
    private MetadataProvider $metadataProvider;
    protected AbusedClient $client;

    protected function getMetadataProvider(): MetadataProvider
    {
        return $this->metadataProvider;
    }

    protected function configureForWsdl(string $wsdl)
    {
        $this->metadataProvider = ExtSoapDriver::createFromClient(
            $this->client = AbusedClient::createFromOptions(
                ExtSoapOptions::defaults($wsdl)
                    ->disableWsdlCache()
            )
        );
    }
}
