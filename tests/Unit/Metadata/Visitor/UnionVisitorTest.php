<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata\Visitor;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\UnionVisitor;

class UnionVisitorTest extends TestCase
{
    function test_it_returns_null_on_invalid_entry()
    {
        $visitor = new UnionVisitor();

        self::assertNull($visitor('list listType {,member1,member2}'));
        self::assertNull($visitor('list listType'));
        self::assertNull($visitor('struct x {}'));
        self::assertNull($visitor('string simpleType'));
    }

    function test_it_returns_type_on_valid_entry()
    {
        $visitor = new UnionVisitor();

        self::assertEquals(
            XsdType::create('unionType')
                ->withBaseType('anyType')
                ->withMemberTypes(['member1','member2']),
            $visitor('union unionType {member1,member2}')
        );

        self::assertEquals(
            XsdType::create('unionType')
                ->withBaseType('anyType'),
            $visitor('union unionType')
        );
    }
}