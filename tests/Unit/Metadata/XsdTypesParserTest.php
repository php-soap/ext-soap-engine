<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Metadata\XsdTypesParser;
use SoapClient;

final class XsdTypesParserTest extends TestCase
{
    private XsdTypesParser $parser;

    protected function setUp(): void
    {
        $this->parser = XsdTypesParser::default();
    }

    public function test_it_contains_a_default_set_of_visitors()
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

        static::assertCount(5, $result);
        static::assertSame('unionType', $result[0]->getName());
        static::assertSame('unionType', $result[1]->getName());
        static::assertSame('listType', $result[2]->getName());
        static::assertSame('listType', $result[3]->getName());
        static::assertSame('simpleType', $result[4]->getName());
    }

    public function test_it_can_handle_double_typenames_in_separate_namespaces()
    {
        $client = $this->createConfiguredMock(SoapClient::class, [
            '__getTypes' => [
                $typeString1 = 'string simpleType',
                $typeString2 = 'integer simpleType',
            ]
        ]);

        $result = $this->parser->parse($client);
        $records = [...$result];

        static::assertCount(2, $result);
        static::assertSame('simpleType', $records[0]->getName());
        static::assertSame('string', $records[0]->getBaseType());
        static::assertSame('simpleType', $records[1]->getName());
        static::assertSame('integer', $records[1]->getBaseType());

        static::assertSame('string', $result->fetchByNameWithFallback('simpleType')->getBaseType());
    }
}
