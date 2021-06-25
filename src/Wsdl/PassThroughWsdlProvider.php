<?php

namespace Phpro\SoapClient\Wsdl\Provider;

use Soap\ExtSoapEngine\Wsdl\WsdlProvider;

/**
 * This provider passes the user input directly as output.
 * It will let the PHP Soap-client handle errors.
 */
class PassThroughWsdlProvider implements WsdlProvider
{
    public function provide(string $source): string
    {
        return $source;
    }
}
