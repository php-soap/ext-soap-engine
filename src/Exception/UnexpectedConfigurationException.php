<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Exception;

use Soap\Engine\Exception\RuntimeException;

class UnexpectedConfigurationException extends RuntimeException
{
    public static function expectedTypeButGot(string $configurationKey, string $expectedType, mixed $value): self
    {
        return new self(
            sprintf(
                'Invalid configuration. Expected value of option %s to be of type %s but got %s.',
                $configurationKey,
                $expectedType,
                gettype($value)
            )
        );
    }
}
