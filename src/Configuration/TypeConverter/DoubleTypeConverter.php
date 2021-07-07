<?php

namespace Soap\ExtSoapEngine\Configuration\TypeConverter;

use DOMDocument;

/**
 * Class DoubleTypeConverter
 *
 * Convert between PHP float and Soap double objects
 */
final class DoubleTypeConverter implements TypeConverterInterface
{
    public function getTypeNamespace(): string
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    public function getTypeName(): string
    {
        return 'double';
    }

    public function convertXmlToPhp(string $xml)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        if ('' === $doc->textContent) {
            return null;
        }

        return (float) $doc->textContent;
    }

    public function convertPhpToXml($php): string
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php);
    }
}
