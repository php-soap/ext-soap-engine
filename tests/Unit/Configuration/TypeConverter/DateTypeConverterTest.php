<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DateTypeConverter;

class DateTypeConverterTest extends TestCase
{
    protected DateTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DateTypeConverter();
    }

    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function testNameIsSpecificValue()
    {
        $this->assertSame('date', $this->converter->getTypeName());
    }

    public function testConvertXmlToPhp()
    {
        $xml = '<date>2019-01-25</date>';

        $php = $this->converter->convertXmlToPhp($xml);

        self::assertInstanceOf(\DateTimeImmutable::class, $php);
        self::assertSame('2019-01-25', $php->format('Y-m-d'));
    }

    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<date/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    public function testConvertPhpToXml()
    {
        $xml = '<date>2019-01-25</date>';

        $output = $this->converter->convertPhpToXml(new \DateTimeImmutable('2019-01-25'));

        $this->assertSame($xml, $output);
    }
}
