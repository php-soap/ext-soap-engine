<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DateTimeTypeConverter;

final class DateTimeTypeConverterTest extends TestCase
{
    protected DateTimeTypeConverter $converter;

    protected function setUp(): void
    {
        date_default_timezone_set('Europe/Brussels');
        $this->converter = new DateTimeTypeConverter();
    }

    public function test_namespace_is_specific_value()
    {
        static::assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function test_name_is_specific_value()
    {
        static::assertSame('dateTime', $this->converter->getTypeName());
    }

    public function test_convert_xml_to_php()
    {
        $xml = '<dateTime>2019-01-25T12:55:00+00:00</dateTime>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertInstanceOf(DateTimeImmutable::class, $php);
        static::assertSame('2019-01-25T13:55:00+01:00', $php->format(DateTimeInterface::ATOM));

        static::assertTrue(
            in_array($php->getTimezone()->getName(), [date('T'), date('P'), date('e')], true)
        );
    }

    public function test_convert_xml_to_php_when_no_text_content()
    {
        $xml = '<dateTime/>';

        $php = $this->converter->convertXmlToPhp($xml);

        static::assertNull($php);
    }

    public function test_convert_php_to_xml()
    {
        $dateTime = new DateTimeImmutable();
        $xml = '<dateTime>'.$dateTime->format('Y-m-d\TH:i:sP').'</dateTime>';

        $output = $this->converter->convertPhpToXml($dateTime);

        static::assertSame($xml, $output);
    }
}
