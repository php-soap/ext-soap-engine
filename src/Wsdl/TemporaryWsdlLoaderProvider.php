<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl;

use Soap\ExtSoapEngine\Wsdl\Naming\NamingStrategy;
use Soap\Wsdl\Loader\WsdlLoader;
use function Psl\Filesystem\write_file;

final class TemporaryWsdlLoaderProvider implements WsdlProvider
{
    public function __construct(
        private WsdlLoader $loader,
        private NamingStrategy $namingStrategy,
        private ?string $cacheDir
    ){
    }

    public function __invoke(string $location): string
    {
        $cacheDir = $this->cacheDir ?? sys_get_temp_dir();
        $file = $cacheDir . DIRECTORY_SEPARATOR . ($this->namingStrategy)($location);

        write_file($file, ($this->loader)($location));

        return $file;
    }
}
