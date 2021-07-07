<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\Classmap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;

final class ClassMapTest extends TestCase
{
    
    public function test_it_tests_class_maps(): void
    {
        $classMap = new ClassMap('wsdlType', 'phpType');

        static::assertSame('wsdlType', $classMap->getWsdlType());
        static::assertSame('phpType', $classMap->getPhpClassName());
    }
}
