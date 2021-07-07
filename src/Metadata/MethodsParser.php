<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;
use SoapClient;

final class MethodsParser
{
    private XsdTypeCollection $xsdTypes;

    public function __construct(XsdTypeCollection $xsdTypes)
    {
        $this->xsdTypes = $xsdTypes;
    }

    public function parse(SoapClient $client): MethodCollection
    {
        return new MethodCollection(...array_map(
            fn (string $methodString) => $this->parseMethodFromString($methodString),
            array_values($client->__getFunctions())
        ));
    }

    private function parseMethodFromString(string $methodString): Method
    {
        $methodString = $this->transformListResponseToArray($methodString);
        return new Method(
            $this->parseName($methodString),
            $this->parseParameters($methodString),
            $this->parseReturnType($methodString)
        );
    }

    private function transformListResponseToArray(string $methodString): string
    {
        return preg_replace('/^list\(([^\)]*)\)(.*)/i', 'array$2', $methodString);
    }

    private function parseParameters(string $methodString): ParameterCollection
    {
        preg_match('/\((.*)\)/', $methodString, $properties);
        if (!$properties[1]) {
            return new ParameterCollection();
        }

        $parameters = preg_split('/,\s?/', $properties[1]);

        return new ParameterCollection(...array_map(
            function (string $parameter): Parameter {
                [$type, $name] = explode(' ', trim($parameter));

                return new Parameter(
                    ltrim($name, '$'),
                    $this->xsdTypes->fetchByNameWithFallback($type)
                );
            },
            $parameters
        ));
    }

    private function parseName(string $methodString): string
    {
        preg_match('/^\w+ (?P<name>\w+)/', $methodString, $matches);

        return $matches['name'];
    }

    private function parseReturnType(string $methodString): XsdType
    {
        preg_match('/^(?P<returnType>\w+)/', $methodString, $matches);

        return $this->xsdTypes->fetchByNameWithFallback($matches['returnType']);
    }
}
