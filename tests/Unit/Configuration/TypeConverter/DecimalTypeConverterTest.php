<?php declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DecimalTypeConverter;

final class DecimalTypeConverterTest extends TestCase
{
    protected DecimalTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DecimalTypeConverter();
    }

    public function test_namespace_is_specific_value()
    {
        static::assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function test_name_is_specific_value()
    {
        static::assertSame('decimal', $this->converter->getTypeName());
    }

    public function test_convert_xml_to_php()
    {
        $xml = '<decimal>24.700</decimal>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertIsFloat($php);
    }

    public function test_convert_xml_to_php_when_no_text_content()
    {
        $xml = '<decimal/>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertNull($php);
    }

    public function test_convert_php_to_xml()
    {
        $xml = '<decimal>24.7</decimal>';

        $output = $this->converter->convertPhpToXml((float) 24.700);

        static::assertSame($xml, $output);
    }
}
