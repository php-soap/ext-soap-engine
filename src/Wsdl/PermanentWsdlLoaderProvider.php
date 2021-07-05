<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl;

use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;
use Soap\ExtSoapEngine\Wsdl\Naming\NamingStrategy;
use Soap\Wsdl\Loader\WsdlLoader;
use function Psl\Filesystem\exists;
use function Psl\Filesystem\write_file;

final class PermanentWsdlLoaderProvider implements WsdlProvider
{
    private bool $downloadForced = false;

    public function __construct(
        private WsdlLoader $loader,
        private ?NamingStrategy $namingStrategy = null,
        private ?string $cacheDir = null
    ){
    }

    public function __invoke(string $location): string
    {
        $cacheDir = $this->cacheDir ?? sys_get_temp_dir();
        $namingStrategy = $this->namingStrategy ?? new Md5Strategy();
        $file = $cacheDir . DIRECTORY_SEPARATOR . $namingStrategy($location);

        if (!$this->downloadForced && exists($file)) {
            return $file;
        }

        write_file($file, ($this->loader)($location));

        return $file;
    }

    /**
     * Makes it possible to refresh permanently stored WSDL files.
     */
    public function forceDownload(): self
    {
        $new = clone $this;
        $new->downloadForced = true;

        return $new;
    }
}
