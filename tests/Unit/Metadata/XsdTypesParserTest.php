<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Metadata\XsdTypesParser;
use SoapClient;

class XsdTypesParserTest extends TestCase
{
    private XsdTypesParser $parser;

    protected function setUp(): void
    {
        $this->parser = XsdTypesParser::default();
    }

    function test_it_contains_a_default_set_of_visitors()
    {
        $client = $this->createConfiguredMock(SoapClient::class, [
            '__getTypes' => [
                $unionString1 = 'union unionType {member1,member2}',
                $unionString2 = 'union unionType',
                $listString1 = 'list listType {member1,member2}',
                $listString2 = 'list listType',
                $simpleTypeString = 'string simpleType',
                $structString = 'struct invalid xsdtype {}'
            ],
        ]);

        $result = [...$this->parser->parse($client)];

        self::assertCount(5, $result);
        self::assertSame('unionType', $result[0]->getName());
        self::assertSame('unionType', $result[1]->getName());
        self::assertSame('listType', $result[2]->getName());
        self::assertSame('listType', $result[3]->getName());
        self::assertSame('simpleType', $result[4]->getName());
    }

    function test_it_can_handle_double_typenames_in_separate_namespaces()
    {
        $client = $this->createConfiguredMock(SoapClient::class, [
            '__getTypes' => [
                $typeString1 = 'string simpleType',
                $typeString2 = 'integer simpleType',
            ]
        ]);

        $result = $this->parser->parse($client);
        $records = [...$result];

        self::assertCount(2, $result);
        self::assertSame('simpleType', $records[0]->getName());
        self::assertSame('string', $records[0]->getBaseType());
        self::assertSame('simpleType', $records[1]->getName());
        self::assertSame('integer', $records[1]->getBaseType());

        self::assertSame('string', $result->fetchByNameWithFallback('simpleType')->getBaseType());
    }
}
