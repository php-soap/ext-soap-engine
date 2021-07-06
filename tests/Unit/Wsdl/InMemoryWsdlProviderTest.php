<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\InMemoryWsdlProvider;

class InMemoryWsdlProviderTest extends TestCase
{
    /** @test */
    public function it_can_provide_a_wsdl(): void
    {
        $provide = new InMemoryWsdlProvider();

        $expected = 'data://text/plain;base64,'.base64_encode('source');
        $actual = $provide('source');

        self::assertSame($expected, $actual);
    }
}
