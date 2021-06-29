<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl;

use Soap\ExtSoapEngine\Wsdl\WsdlProvider;

/**
 * This provider passes the user input directly as output.
 * It will let the PHP Soap-client handle errors.
 */
class PassThroughWsdlProvider implements WsdlProvider
{
    public function __invoke(string $source): string
    {
        return $source;
    }
}
