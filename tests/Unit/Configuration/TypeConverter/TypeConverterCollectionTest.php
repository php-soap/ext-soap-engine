<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DateTimeTypeConverter;
use Soap\ExtSoapEngine\Configuration\TypeConverter\TypeConverterCollection;

class TypeConverterCollectionTest extends TestCase
{
    /** @test */
    public function it_tests_type_collections(): void
    {
        $collection = new TypeConverterCollection(
            [$converter = new DateTimeTypeConverter()]
        );
        $data = iterator_to_array($collection);

        self::assertCount(1, $data);
        self::assertSame([
            'http://www.w3.org/2001/XMLSchema:dateTime' => $converter,
        ], $data);
    }

    /** @test */
    public function it_can_add_types(): void
    {
        $collection = new TypeConverterCollection();
        $collection->set($converter = new DateTimeTypeConverter());
        $data = iterator_to_array($collection);

        self::assertSame([
            'http://www.w3.org/2001/XMLSchema:dateTime' => $converter,
        ], $data);
    }
}