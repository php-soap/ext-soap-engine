<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\ErrorHandling;

use Soap\ExtSoapEngine\Exception\RequestException;

/**
 * @psalm-internal Soap\ExtSoapEngine
 */
final class ExtSoapErrorHandler
{
    private function __construct()
    {
    }

    /**
     * @template T
     *
     * @param (callable(): T) $fun
     *
     * @return array{0: T, 1: ?string}
     *
     * @psalm-suppress MissingThrowsDocblock
     */
    public function __invoke(callable $fun): array
    {
        $lastMessage = null;
        /** @psalm-suppress InvalidArgument */
        set_error_handler(static function (int $_type, string $message) use (&$lastMessage) {
            $lastMessage = $message;
        });

        try {
            $value = $fun();

            /** @var array{0: T, 1: ?string} $result */
            $result = [$value, $lastMessage];

            return $result;
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @template T
     *
     * @param (callable(): T) $fun
     *
     * @return T
     */
    public static function handleInternalErrors(callable $fun)
    {
        [$result, $lastMessage] = (new self)($fun);

        if ($lastMessage !== null) {
            throw RequestException::internalSoapError($lastMessage);
        }

        return $result;
    }

    /**
     * @template T
     *
     * @param T $response
     * @psalm-assert !null $response
     *
     * @return T
     */
    public static function handleNullResponse($response)
    {
        if ($response === null) {
            throw RequestException::internalSoapError(
                'An empty response got returned after contacting the SOAP server.'
            );
        }

        return $response;
    }
}
