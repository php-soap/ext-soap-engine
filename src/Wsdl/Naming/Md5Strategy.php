<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl\Naming;

final class Md5Strategy implements NamingStrategy
{
    public function __invoke(string $location): string
    {
        return md5($location).'.wsdl';
    }
}
