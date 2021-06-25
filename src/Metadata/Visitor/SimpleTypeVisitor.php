<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata\Visitor;

use Soap\Engine\Metadata\Model\XsdType;

final class SimpleTypeVisitor implements XsdTypeVisitorInterface
{
    public function __invoke(string $soapType): ?XsdType
    {
        if (!preg_match('/^(?!list|union|struct)(?P<baseType>\w+) (?P<typeName>\w+)/', $soapType, $matches)) {
            return null;
        }

        return XsdType::create($matches['typeName'])
            ->withBaseType($matches['baseType']);
    }
}
