<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\PassThroughWsdlProvider;

class PassThroughWsdlProviderTest extends TestCase
{
    /** @test */
    public function it_can_provide_a_wsdl(): void
    {
        $provide = new PassThroughWsdlProvider();

        $actual = $provide($expected = 'some.wsdl');

        self::assertSame($expected, $actual);
    }
}
