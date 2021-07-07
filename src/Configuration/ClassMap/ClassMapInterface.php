<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\Configuration\ClassMap;

interface ClassMapInterface
{
    public function getWsdlType(): string;
    public function getPhpClassName(): string;
}
