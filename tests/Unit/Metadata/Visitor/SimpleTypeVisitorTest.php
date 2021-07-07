<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Metadata\Visitor;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Metadata\Visitor\SimpleTypeVisitor;

final class SimpleTypeVisitorTest extends TestCase
{
    public function test_it_returns_null_on_invalid_entry()
    {
        $visitor = new SimpleTypeVisitor();

        static::assertNull($visitor('list listType {,member1,member2}'));
        static::assertNull($visitor('list listType'));
        static::assertNull($visitor('union unionType {,member1,member2}'));
        static::assertNull($visitor('union unionType'));
        static::assertNull($visitor('struct x {}'));
    }

    public function test_it_returns_type_on_valid_entry()
    {
        $visitor = new SimpleTypeVisitor();
        static::assertEquals(
            XsdType::create('simpleType')
                ->withBaseType('string'),
            $visitor('string simpleType')
        );
    }
}
