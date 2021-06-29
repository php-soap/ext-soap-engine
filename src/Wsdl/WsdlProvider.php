<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine\Wsdl;

interface WsdlProvider
{
    /**
     * This method can be used to transform a location into another location.
     * The output needs to be processable by the SoapClient $wsdl option.
     */
    public function __invoke(string $location): string;
}