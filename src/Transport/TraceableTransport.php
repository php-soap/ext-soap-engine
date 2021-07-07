<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Transport;

use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\Engine\Transport;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\HttpBinding\LastRequestInfo;

final class TraceableTransport implements Transport
{
    private AbusedClient $client;
    private Transport $transport;
    private LastRequestInfo $lastRequestInfo;

    public function __construct(AbusedClient $client, Transport $transport)
    {
        $this->client = $client;
        $this->transport = $transport;
        $this->lastRequestInfo = LastRequestInfo::empty();
    }

    /**
     * @psalm-suppress RedundantCastGivenDocblockType - Whatever psalm says ... Request|Response headers can be null :-(
     */
    public function request(SoapRequest $request): SoapResponse
    {
        $response = $this->transport->request($request);

        $this->lastRequestInfo = new LastRequestInfo(
            (string) $this->client->__getLastRequestHeaders(),
            $request->getRequest(),
            (string) $this->client->__getLastResponseHeaders(),
            $response->getPayload()
        );

        return $response;
    }

    public function collectLastRequestInfo(): LastRequestInfo
    {
        return $this->lastRequestInfo;
    }
}
