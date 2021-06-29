<?php

declare( strict_types=1 );

namespace Soap\ExtSoapEngine\Wsdl;

/**
 * This provider can be used to pass in a raw WSDL file.
 */
class InMemoryWsdlProvider implements WsdlProvider
{
    public function __invoke(string $location): string
    {
        return 'data://text/plain;base64,'.base64_encode($location);
    }
}
