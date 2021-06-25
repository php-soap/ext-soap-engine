<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Generator;

use Soap\Engine\Metadata\Metadata;

/**
 * For decoding the soap response, we require that the __soapCall takes the same amount of arguments.
 * If a, this causes segfaults when using a type map.
 */
class DummyMethodArgumentsGenerator
{
    /**
     * @var Metadata
     */
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function generateForSoapCall(string $method): array
    {
        $methods = $this->metadata->getMethods();
        $method = $methods->fetchByName($method);

        return array_fill(0, \count($method->getParameters()), null);
    }
}
