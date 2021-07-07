<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Metadata;

use Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\ListVisitor;
use Soap\ExtSoapEngine\Metadata\Visitor\SimpleTypeVisitor;
use Soap\ExtSoapEngine\Metadata\Visitor\UnionVisitor;
use Soap\ExtSoapEngine\Metadata\Visitor\XsdTypeVisitorInterface;
use SoapClient;

final class XsdTypesParser
{
    /**
     * @var XsdTypeVisitorInterface[]
     */
    private array $visitors;

    public function __construct(XsdTypeVisitorInterface ...$visitors)
    {
        $this->visitors = $visitors;
    }

    public static function default(): self
    {
        return new self(
            new ListVisitor(),
            new UnionVisitor(),
            new SimpleTypeVisitor()
        );
    }

    public function parse(SoapClient $client): XsdTypeCollection
    {
        $collected = [];
        $soapTypes = $client->__getTypes();
        foreach ($soapTypes as $soapType) {
            if ($type = $this->detectXsdType($soapType)) {
                $collected[] = $type;
            }
        }

        return new XsdTypeCollection(...$collected);
    }

    private function detectXsdType(string $soapType): ?XsdType
    {
        foreach ($this->visitors as $visitor) {
            if ($type = $visitor($soapType)) {
                return $type;
            }
        }

        return null;
    }
}
