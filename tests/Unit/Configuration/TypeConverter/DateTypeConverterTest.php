<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DateTypeConverter;

final class DateTypeConverterTest extends TestCase
{
    protected DateTypeConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new DateTypeConverter();
    }

    public function test_namespace_is_specific_value()
    {
        static::assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function test_name_is_specific_value()
    {
        static::assertSame('date', $this->converter->getTypeName());
    }

    public function test_convert_xml_to_php()
    {
        $xml = '<date>2019-01-25</date>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertInstanceOf(DateTimeImmutable::class, $php);
        static::assertSame('2019-01-25', $php->format('Y-m-d'));
    }

    public function test_convert_xml_to_php_when_no_text_content()
    {
        $xml = '<date/>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertNull($php);
    }

    public function test_convert_php_to_xml()
    {
        $xml = '<date>2019-01-25</date>';

        $output = $this->converter->convertPhpToXml(new DateTimeImmutable('2019-01-25'));

        static::assertSame($xml, $output);
    }
}
