<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl;

use Psl\File\WriteMode;
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;
use Soap\ExtSoapEngine\Wsdl\Naming\NamingStrategy;
use Soap\Wsdl\Loader\WsdlLoader;
use function Psl\File\write;

final class TemporaryWsdlLoaderProvider implements WsdlProvider
{
    public function __construct(
        private WsdlLoader $loader,
        private ?NamingStrategy $namingStrategy = null,
        private ?string $cacheDir = null
    ) {
    }

    public function __invoke(string $location): string
    {
        $cacheDir = $this->cacheDir ?? sys_get_temp_dir();
        $namingStrategy = $this->namingStrategy ?? new Md5Strategy();
        $file = $cacheDir . DIRECTORY_SEPARATOR . $namingStrategy($location);

        write($file, ($this->loader)($location), WriteMode::TRUNCATE);

        return $file;
    }
}
