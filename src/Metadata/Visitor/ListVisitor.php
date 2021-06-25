<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata\Visitor;

use Soap\Engine\Metadata\Model\XsdType;

class ListVisitor implements XsdTypeVisitorInterface
{
    public function __invoke(string $soapType): ?XsdType
    {
        if (!preg_match('/^list (?P<typeName>\w+)( \{(?P<memberTypes>[^\}]+)\})?$/', $soapType, $matches)) {
            return null;
        }

        $type = XsdType::create($matches['typeName'])
            ->withBaseType('array');

        if ($memberTypes = $matches['memberTypes'] ?? '') {
            $type = $type->withMemberTypes(explode(',', $memberTypes));
        }

        return $type;
    }
}
