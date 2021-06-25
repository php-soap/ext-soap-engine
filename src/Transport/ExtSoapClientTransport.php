<?php

namespace Soap\ExtSoapEngine\Transport;

use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\Engine\Transport;
use Soap\ExtSoapEngine\AbusedClient;

final class ExtSoapClientTransport implements Transport
{
    private AbusedClient $client;

    public function __construct(AbusedClient $client)
    {
        $this->client = $client;
    }

    public function request(SoapRequest $request): SoapResponse
    {
        $response = $this->client->doActualRequest(
            $request->getRequest(),
            $request->getLocation(),
            $request->getAction(),
            $request->getVersion(),
            $request->getOneWay()
        );

        return new SoapResponse($response);
    }
}
