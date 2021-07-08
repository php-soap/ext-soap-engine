<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl\Naming;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;

final class Md5StrategyTest extends TestCase
{
    public function test_it_can_name_a_location(): void
    {
        $name = new Md5Strategy();
        $actual = $name($location = 'http://some.com/location.wsdl');
        $expected = md5($location).'.wsdl';

        static::assertSame($expected, $actual);
    }
}
