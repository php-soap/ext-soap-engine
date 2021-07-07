<?php declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DoubleTypeConverter;

final class DoubleTypeConverterTest extends TestCase
{
    protected DoubleTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DoubleTypeConverter();
    }

    public function test_namespace_is_specific_value()
    {
        static::assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function test_name_is_specific_value()
    {
        static::assertSame('double', $this->converter->getTypeName());
    }

    public function test_convert_xml_to_php()
    {
        $xml = '<double>24.700</double>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertIsFloat($php);
    }

    public function test_convert_xml_to_php_when_no_text_content()
    {
        $xml = '<double/>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertNull($php);
    }

    public function test_convert_php_to_xml()
    {
        $xml = '<double>24.7</double>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        static::assertSame($xml, $output);
    }
}
