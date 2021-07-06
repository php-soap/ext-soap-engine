<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl\Naming;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;

class Md5StrategyTest extends TestCase
{
    /** @test */
    public function it_can_name_a_location(): void
    {
        $name = new Md5Strategy();
        $actual = $name($location = 'http://some.com/location.wsdl');
        $expected = md5($location).'.wsdl';

        self::assertSame($expected, $actual);
    }
}