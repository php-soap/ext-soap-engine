<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\TemporaryWsdlLoaderProvider;
use Soap\Wsdl\Loader\WsdlLoader;

final class TemporaryWsdlLoaderProviderTest extends TestCase
{
    public function test_it_can_provide_a_wsdl(): void
    {
        $loader = $this->createConfiguredMock(WsdlLoader::class, [
            '__invoke' => $content = '<definitions />'
        ]);

        $provide = new TemporaryWsdlLoaderProvider($loader);
        $file = $provide('some.wsdl');

        try {
            static::assertStringStartsWith(sys_get_temp_dir(), $file);
            static::assertFileExists($file);
            static::assertStringEqualsFile($file, $content);
        } finally {
            @unlink($file);
        }
    }
}
