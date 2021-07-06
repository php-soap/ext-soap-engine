<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\XsdTypeCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\TypesParser;
use SoapClient;

class TypesParserTest extends TestCase
{
    private TypesParser $parser;

    protected function setUp(): void
    {
        $this->parser = new TypesParser(
            new XsdTypeCollection(
                XsdType::create('simpleType')->withBaseType('string')
            )
        );
    }

    function test_it_can_parse_ext_soap_types_strings_with_single_argument()
    {
        $client = $this->createConfiguredMock(SoapClient::class, [
            '__getTypes' => [
                'string simpleType',
                'union unionType {string, integer}',
                'list listType {integer}',
                <<<EOSTRUCT
                struct ProductLine {
                 string Mode;
                 string RelevanceRank;
                 ProductInfo ProductInfo;
                 simpleType xsdType;
                }
                EOSTRUCT
            ],
        ]);

        $types = $this->parser->parse($client);

        self::assertCount(1, $types);

        $type = $types->fetchFirstByName('ProductLine');
        self::assertSame('ProductLine', $type->getName());

        $properties = [...$type->getProperties()];
        self::assertCount(4, $properties);

        self::assertEquals(
            new Property('Mode', XsdType::create('string')),
            $properties[0]
        );
        self::assertEquals(
            new Property('RelevanceRank', XsdType::create('string')),
            $properties[1]
        );
        self::assertEquals(
            new Property('ProductInfo', XsdType::create('ProductInfo')),
            $properties[2]
        );
        self::assertEquals(
            new Property('xsdType', XsdType::create('simpleType')->withBaseType('string')),
            $properties[3]
        );
    }
}
