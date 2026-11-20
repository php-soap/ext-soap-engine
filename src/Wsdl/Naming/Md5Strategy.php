<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl\Naming;

final class Md5Strategy implements NamingStrategy
{
    public function __invoke(string $location): string
    {
        /** @psalm-suppress WeakHash - The hash only names a cache file, it has no security purpose. */
        return md5($location).'.wsdl';
    }
}
