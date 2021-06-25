<?php

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DecimalTypeConverter;

class DecimalTypeConverterTest extends TestCase
{
    protected DecimalTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DecimalTypeConverter();
    }

    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function testNameIsSpecificValue()
    {
        $this->assertSame('decimal', $this->converter->getTypeName());
    }

    public function testConvertXmlToPhp()
    {
        $xml = '<decimal>24.700</decimal>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertIsFloat($php);
    }

    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<decimal/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    public function testConvertPhpToXml()
    {
        $xml = '<decimal>24.7</decimal>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        $this->assertSame($xml, $output);
    }
}
