<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata\Visitor;

use Soap\Engine\Metadata\Model\XsdType;

interface XsdTypeVisitorInterface
{
    public function __invoke(string $soapType): ?XsdType;
}
