<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata\Visitor;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\SimpleTypeVisitor;

class SimpleTypeVisitorTest extends TestCase
{
    function test_it_returns_null_on_invalid_entry()
    {
        $visitor = new SimpleTypeVisitor();

        self::assertNull($visitor('list listType {,member1,member2}'));
        self::assertNull($visitor('list listType'));
        self::assertNull($visitor('union unionType {,member1,member2}'));
        self::assertNull($visitor('union unionType'));
        self::assertNull($visitor('struct x {}'));
    }

    function test_it_returns_type_on_valid_entry()
    {
        $visitor = new SimpleTypeVisitor();
        self::assertEquals(
            XsdType::create('simpleType')
                ->withBaseType('string'),
            $visitor('string simpleType')
        );
    }
}