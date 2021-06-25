<?php

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DoubleTypeConverter;

class DoubleTypeConverterTest extends TestCase
{
    protected DoubleTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DoubleTypeConverter();
    }

    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function testNameIsSpecificValue()
    {
        $this->assertSame('double', $this->converter->getTypeName());
    }

    public function testConvertXmlToPhp()
    {
        $xml = '<double>24.700</double>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertIsFloat($php);
    }

    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<double/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    public function testConvertPhpToXml()
    {
        $xml = '<double>24.7</double>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        $this->assertSame($xml, $output);
    }
}
