<?php declare(strict_types=1);

namespace Soap\ExtSoapEngine\Configuration\TypeConverter;

use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;

/**
 * Class DateTypeConverter
 *
 * Converts between PHP \DateTime and SOAP date objects
 */
final class DateTypeConverter implements TypeConverterInterface
{
    public function getTypeNamespace(): string
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    public function getTypeName(): string
    {
        return 'date';
    }

    public function convertXmlToPhp(string $xml)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        if ('' === $doc->textContent) {
            return null;
        }

        return new DateTimeImmutable($doc->textContent);
    }

    public function convertPhpToXml($php): string
    {
        if (!$php instanceof DateTimeInterface) {
            return '';
        }

        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d'));
    }
}
