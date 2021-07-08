<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\InMemoryWsdlProvider;

final class InMemoryWsdlProviderTest extends TestCase
{
    public function test_it_can_provide_a_wsdl(): void
    {
        $provide = new InMemoryWsdlProvider();

        $expected = 'data://text/plain;base64,'.base64_encode('source');
        $actual = $provide('source');

        static::assertSame($expected, $actual);
    }
}
