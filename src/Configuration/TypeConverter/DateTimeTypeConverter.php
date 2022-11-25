<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\Configuration\TypeConverter;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DOMDocument;

/**
 * Class DateTimeTypeConverter
 *
 * Converts between PHP \DateTime and SOAP dateTime objects
 */
final class DateTimeTypeConverter implements TypeConverterInterface
{
    public function getTypeNamespace(): string
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    public function getTypeName(): string
    {
        return 'dateTime';
    }

    /**
     * @param non-empty-string $xml
     */
    public function convertXmlToPhp(string $xml)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        if ('' === $doc->textContent) {
            return null;
        }

        $dateTime = new DateTimeImmutable($doc->textContent);

        return $dateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
    }

    public function convertPhpToXml($php): string
    {
        if (!$php instanceof DateTimeInterface) {
            return '';
        }

        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d\TH:i:sP'));
    }
}
