<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;


use Soap\Engine\Engine;
use Soap\Engine\SimpleEngine;
use Soap\Engine\Transport;
use Soap\ExtSoapEngine\Transport\ExtSoapClientTransport;

class ExtSoapEngineFactory
{
    public static function fromOptions(ExtSoapOptions $options): SimpleEngine
    {
        $driver = ExtSoapDriver::createFromOptions($options);
        $handler = new ExtSoapClientTransport($driver->getClient());

        return new SimpleEngine($driver, $handler);
    }

    public static function fromOptionsWithTransport(ExtSoapOptions $options, Transport $transport): SimpleEngine
    {
        $driver = ExtSoapDriver::createFromOptions($options);

        return new SimpleEngine($driver, $transport);
    }
}
