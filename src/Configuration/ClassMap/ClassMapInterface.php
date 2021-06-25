<?php

namespace Soap\ExtSoapEngine\Configuration\ClassMap;

interface ClassMapInterface
{
    public function getWsdlType(): string;
    public function getPhpClassName(): string;
}
