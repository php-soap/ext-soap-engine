<?php
declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl\Naming;

interface NamingStrategy
{
    /**
     * Returns a valid filename for storing a file
     */
    public function __invoke(string $location): string;
}