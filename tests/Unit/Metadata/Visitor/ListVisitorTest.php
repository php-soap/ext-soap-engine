<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata\Visitor;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\ListVisitor;

final class ListVisitorTest extends TestCase
{
    public function test_it_returns_null_on_invalid_entry()
    {
        $visitor = new ListVisitor();

        static::assertNull($visitor('union unionType {member1,member2}'));
        static::assertNull($visitor('union unionType'));
        static::assertNull($visitor('struct x {}'));
        static::assertNull($visitor('string simpleType'));
    }

    public function test_it_returns_type_on_valid_entry()
    {
        $visitor = new ListVisitor();

        static::assertEquals(
            XsdType::create('listType')
                ->withBaseType('array')
                ->withMemberTypes(['member1','member2']),
            $visitor('list listType {,member1,member2}')
        );
        static::assertEquals(
            XsdType::create('listType')
                ->withBaseType('array'),
            $visitor('list listType')
        );
    }
}
