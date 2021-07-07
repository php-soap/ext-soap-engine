<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\HttpBinding;

final class LastRequestInfo
{
    private string $lastRequestHeaders;
    private string $lastRequest;
    private string $lastResponseHeaders;
    private string $lastResponse;

    public function __construct(
        string $lastRequestHeaders,
        string $lastRequest,
        string $lastResponseHeaders,
        string $lastResponse
    ) {
        $this->lastRequestHeaders = $lastRequestHeaders;
        $this->lastRequest = $lastRequest;
        $this->lastResponseHeaders = $lastResponseHeaders;
        $this->lastResponse = $lastResponse;
    }

    public static function empty(): self
    {
        return new self('', '', '', '');
    }

    public function getLastRequestHeaders(): string
    {
        return $this->lastRequestHeaders;
    }

    public function getLastRequest(): string
    {
        return $this->lastRequest;
    }

    public function getLastResponseHeaders(): string
    {
        return $this->lastResponseHeaders;
    }

    public function getLastResponse(): string
    {
        return $this->lastResponse;
    }
}
