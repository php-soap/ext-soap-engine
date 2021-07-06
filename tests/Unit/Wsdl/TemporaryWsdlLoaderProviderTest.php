<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\TemporaryWsdlLoaderProvider;
use Soap\Wsdl\Loader\WsdlLoader;

class TemporaryWsdlLoaderProviderTest extends TestCase
{
    /** @test */
    public function it_can_provide_a_wsdl(): void
    {
        $loader = $this->createConfiguredMock(WsdlLoader::class, [
            '__invoke' => $content = '<definitions />'
        ]);

        $provide = new TemporaryWsdlLoaderProvider($loader);
        $file = $provide('some.wsdl');

        try {

            self::assertStringStartsWith(sys_get_temp_dir(), $file);
            self::assertFileExists($file);
            self::assertStringEqualsFile($file, $content);
        } finally {
            @unlink($file);
        }
    }
}
