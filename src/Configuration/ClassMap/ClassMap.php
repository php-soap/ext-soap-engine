<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\Configuration\ClassMap;

final class ClassMap implements ClassMapInterface
{
    private string $wsdlType;
    private string $phpClassName;

    public function __construct(string $wsdlType, string $phpClassName)
    {
        $this->wsdlType = $wsdlType;
        $this->phpClassName = $phpClassName;
    }

    public function getPhpClassName(): string
    {
        return $this->phpClassName;
    }

    public function getWsdlType(): string
    {
        return $this->wsdlType;
    }
}
