<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\PassThroughWsdlProvider;

final class PassThroughWsdlProviderTest extends TestCase
{
    public function test_it_can_provide_a_wsdl(): void
    {
        $provide = new PassThroughWsdlProvider();

        $actual = $provide($expected = 'some.wsdl');

        static::assertSame($expected, $actual);
    }
}
