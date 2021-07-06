<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata\Visitor;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\ListVisitor;

class ListVisitorTest extends TestCase
{
    function test_it_returns_null_on_invalid_entry()
    {
        $visitor = new ListVisitor();

        self::assertNull($visitor('union unionType {member1,member2}'));
        self::assertNull($visitor('union unionType'));
        self::assertNull($visitor('struct x {}'));
        self::assertNull($visitor('string simpleType'));
    }

    function test_it_returns_type_on_valid_entry()
    {
        $visitor = new ListVisitor();

        self::assertEquals(
            XsdType::create('listType')
                ->withBaseType('array')
                ->withMemberTypes(['member1','member2']),
            $visitor('list listType {,member1,member2}')
        );
        self::assertEquals(
            XsdType::create('listType')
                ->withBaseType('array'),
            $visitor('list listType')
        );
    }
}
