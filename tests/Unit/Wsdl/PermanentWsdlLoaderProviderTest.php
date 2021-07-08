<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Wsdl;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Wsdl\PermanentWsdlLoaderProvider;
use Soap\Wsdl\Loader\WsdlLoader;

final class PermanentWsdlLoaderProviderTest extends TestCase
{
    public function test_it_can_provide_a_wsdl(): void
    {
        $loader = $this->createConfiguredMock(WsdlLoader::class, [
            '__invoke' => $content = '<definitions />'
        ]);

        $provide = new PermanentWsdlLoaderProvider($loader);
        $file = $provide('some.wsdl');

        try {
            static::assertStringStartsWith(sys_get_temp_dir(), $file);
            static::assertFileExists($file);
            static::assertStringEqualsFile($file, $content);
        } finally {
            @unlink($file);
        }
    }

    
    public function test_it_only_fetches_wsdl_once(): void
    {
        $loader = $this->createConfiguredMock(WsdlLoader::class, [
            '__invoke' => $content = '<definitions />'
        ]);
        $loader->expects(static::once())->method('__invoke');

        $provide = new PermanentWsdlLoaderProvider($loader);
        $provide('some.wsdl');
        $provide('some.wsdl');
        $file = $provide('some.wsdl');

        try {
            static::assertStringStartsWith(sys_get_temp_dir(), $file);
            static::assertFileExists($file);
            static::assertStringEqualsFile($file, $content);
        } finally {
            @unlink($file);
        }
    }

    
    public function test_it_fetches_multiple_times_for_forced_downloads(): void
    {
        $loader = $this->createConfiguredMock(WsdlLoader::class, [
            '__invoke' => $content = '<definitions />'
        ]);
        $loader->expects(static::exactly(2))->method('__invoke');

        $provide = (new PermanentWsdlLoaderProvider($loader))->forceDownload();
        $provide('some.wsdl');
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
