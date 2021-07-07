<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\Engine\HttpBinding\SoapRequest;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\ExtSoapEngine\Exception\RequestException;

final class AbusedClient extends \SoapClient
{
    private ?SoapRequest $storedRequest = null;
    private ?SoapResponse $storedResponse = null;

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

    public function __construct(?string $wsdl, array $options = [])
    {
        $options = ExtSoapOptionsResolverFactory::createForWsdl($wsdl)->resolve($options);
        parent::__construct($wsdl, $options);
    }

    public static function createFromOptions(ExtSoapOptions $options): self
    {
        return new self($options->getWsdl(), $options->getOptions());
    }

    /**
     * @psalm-suppress RedundantCastGivenDocblockType - Whatever psalm says ... $oneWay can be bool :-(
     */
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
        $this->__last_request = $request;
        $this->__last_response = parent::__doRequest($request, $location, $action, $version, $oneWay);

        return $this->__last_response;
    }

    public function collectRequest(): SoapRequest
    {
        if (!$this->storedRequest) {
            throw RequestException::noRequestWasMadeYet();
        }

        return $this->storedRequest;
    }

    public function registerResponse(SoapResponse $response): void
    {
        $this->storedResponse = $response;
    }

    public function cleanUpTemporaryState(): void
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
