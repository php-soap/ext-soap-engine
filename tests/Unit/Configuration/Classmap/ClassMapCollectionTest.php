<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\Classmap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;

final class ClassMapCollectionTest extends TestCase
{
    
    public function test_it_tests_class_maps(): void
    {
        $classMap = new ClassMapCollection(
            $item1 = new ClassMap('wsdlType', 'phpType'),
            new ClassMap('double', 'double'),
            $item2 = new ClassMap('double', 'double'),
        );

        static::assertCount(2, $classMap);
        static::assertSame([
            'wsdlType' => $item1,
            'double' => $item2,
        ], iterator_to_array($classMap));
    }

    
    public function test_it_can_add_types(): void
    {
        $classMap = new ClassMapCollection();
        $classMap->set($item1 = new ClassMap('wsdlType', 'phpType'));
        $classMap->set($item2 = new ClassMap('wsdlType', 'phpType'));

        static::assertSame([
            'wsdlType' => $item2,
        ], iterator_to_array($classMap));
    }
}
