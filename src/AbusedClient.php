<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;

final class AbusedClient extends \SoapClient
{
    private ?SoapRequest $storedRequest;
    private ?SoapResponse $storedResponse;

    // @codingStandardsIgnoreStart
    /**
     * Internal SoapClient property for storing last request.
     *
     * @var string
     */
    protected $__last_request = '';
    // @codingStandardsIgnoreEnd

    // @codingStandardsIgnoreStart
    /**
     * Internal SoapClient property for storing last response.
     *
     * @var string
     */
    protected $__last_response = '';
    // @codingStandardsIgnoreEnd

    public function __construct($wsdl, array $options = [])
    {
        $options = ExtSoapOptionsResolverFactory::createForWsdl($wsdl)->resolve($options);
        parent::__construct($wsdl, $options);
    }

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        return new self($options->getWsdl(), $options->getOptions());
    }

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        $this->storedRequest = new SoapRequest($request, $location, $action, $version, (int) $oneWay);

        return $this->storedResponse ? $this->storedResponse->getPayload() : '';
    }

    public function doActualRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        int $oneWay = 0
    ): string {
        $typedOneWay = PHP_VERSION_ID >= 80000 ? (bool) $oneWay : $oneWay;
        $this->__last_request = $request;
        $this->__last_response = (string) parent::__doRequest($request, $location, $action, $version, $typedOneWay);

        return $this->__last_response;
    }

    public function collectRequest(): SoapRequest
    {
        if (!$this->storedRequest) {
            // TODO : use real exception
            throw new \RuntimeException('No request has been registered yet.');
        }

        return $this->storedRequest;
    }

    public function registerResponse(SoapResponse $response)
    {
        $this->storedResponse = $response;
    }

    public function cleanUpTemporaryState()
    {
        $this->storedRequest = null;
        $this->storedResponse = null;
    }

    public function __getLastRequest() : string
    {
        return $this->__last_request;
    }

    public function __getLastResponse() : string
    {
        return $this->__last_response;
    }
}
