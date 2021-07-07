<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\Transport;

use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\Engine\Transport;
use SoapServer;

final class ExtSoapServerTransport implements Transport
{
    private SoapServer $server;

    public function __construct(SoapServer $server)
    {
        $this->server = $server;
    }

    public function request(SoapRequest $request): SoapResponse
    {
        ob_start();
        $this->server->handle($request->getRequest());
        $responseBody = ob_get_contents();
        ob_end_clean();

        return new SoapResponse($responseBody);
    }
}
