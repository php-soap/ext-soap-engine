<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\Classmap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;

class ClassMapTest extends TestCase
{
    /** @test */
    public function it_tests_class_maps(): void
    {
        $classMap = new ClassMap('wsdlType', 'phpType');

        self::assertSame('wsdlType', $classMap->getWsdlType());
        self::assertSame('phpType', $classMap->getPhpClassName());
    }
}