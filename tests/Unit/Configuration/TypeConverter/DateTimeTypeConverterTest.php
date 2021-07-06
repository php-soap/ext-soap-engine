<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Configuration\TypeConverter;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\TypeConverter\DateTimeTypeConverter;

class DateTimeTypeConverterTest extends TestCase
{
    protected DateTimeTypeConverter $converter;

    protected function setUp(): void
    {
        date_default_timezone_set('Europe/Brussels');
        $this->converter = new DateTimeTypeConverter();
    }

    public function testNamespaceIsSpecificValue()
    {
        $this->assertSame('http://www.w3.org/2001/XMLSchema', $this->converter->getTypeNamespace());
    }

    public function testNameIsSpecificValue()
    {
        $this->assertSame('dateTime', $this->converter->getTypeName());
    }

    public function testConvertXmlToPhp()
    {
        $xml = '<dateTime>2019-01-25T12:55:00+00:00</dateTime>';

        $php = $this->converter->convertXmlToPhp($xml);

        self::assertInstanceOf(\DateTimeImmutable::class, $php);
        self::assertSame('2019-01-25T13:55:00+01:00', $php->format(\DateTimeInterface::ATOM));

        self::assertTrue(
            in_array($php->getTimezone()->getName(), [date('T'), date('P'), date('e')], true)
        );
    }

    public function testConvertXmlToPhpWhenNoTextContent()
    {
        $xml = '<dateTime/>';

        $php = $this->converter->convertXmlToPhp($xml);

        $this->assertNull($php);
    }

    public function testConvertPhpToXml()
    {
        $dateTime = new \DateTimeImmutable();
        $xml = '<dateTime>'.$dateTime->format('Y-m-d\TH:i:sP').'</dateTime>';

        $output = $this->converter->convertPhpToXml($dateTime);

        $this->assertSame($xml, $output);
    }
}