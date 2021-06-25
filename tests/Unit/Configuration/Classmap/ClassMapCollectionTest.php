<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\Classmap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;

class ClassMapCollectionTest extends TestCase
{
    /** @test */
    public function it_tests_class_maps(): void
    {
        $classMap = new ClassMapCollection(
            $item1 = new ClassMap('wsdlType', 'phpType'),
            new ClassMap('double', 'double'),
            $item2 = new ClassMap('double', 'double'),
        );

        self::assertCount(2, $classMap);
        self::assertSame([
            'wsdlType' => $item1,
            'double' => $item2,
        ], iterator_to_array($classMap));
    }
}