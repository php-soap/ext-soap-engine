<?php

namespace Soap\ExtSoapEngine\Wsdl;

/**
 * This provider collects the source based on a loader callback.
 * The loader can e.g. download the wsdl and store it at an internal location.
 */
class CallbackWsdlProvider implements WsdlProvider
{
    /**
     * @var callable(string): string
     */
    private $loader;

    public function __construct(callable $loader)
    {
        $this->loader = $loader;
    }

    public function __invoke(string $location): string
    {
        return ($this->loader)($location);
    }
}
