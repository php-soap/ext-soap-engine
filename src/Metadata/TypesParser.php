<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata;

use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use Soap\Engine\Metadata\Model\XsdType;

final class TypesParser
{
    /**
     * @var XsdTypeCollection
     */
    private $xsdTypes;

    public function __construct(XsdTypeCollection $xsdTypes)
    {
        $this->xsdTypes = $xsdTypes;
    }

    public function parse(AbusedClient $abusedClient): TypeCollection
    {
        $collected = [];
        $soapTypes = $abusedClient->__getTypes();
        foreach ($soapTypes as $soapType) {
            $properties = [];
            $lines = explode("\n", $soapType);
            if (!preg_match('/struct (?P<typeName>.*) {/', $lines[0], $matches)) {
                continue;
            }
            $xsdType = XsdType::create($matches['typeName']);

            foreach (array_slice($lines, 1) as $line) {
                if ($line === '}') {
                    continue;
                }
                preg_match('/\s* (?P<propertyType>.*) (?P<propertyName>.*);/', $line, $matches);
                $properties[] = new Property(
                    $matches['propertyName'],
                    $this->xsdTypes->fetchByNameWithFallback($matches['propertyType'])
                );
            }

            $collected[] = new Type($xsdType, new PropertyCollection(...$properties));
        }

        return new TypeCollection(...$collected);
    }
}
