<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Functional\ExtSoap;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\ExtSoapOptionsResolverFactory;
use Soap\ExtSoapEngine\Transport\ExtSoapServerTransport;
use SoapServer;

abstract class AbstractSoapTestCase extends TestCase
{
    protected function configureSoapDriver(string $wsdl, array $options): ExtSoapDriver
    {
        return ExtSoapDriver::createFromOptions(
            ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache()
        );
    }

    protected function configureServer(string $wsdl, array $options, $object): ExtSoapServerTransport
    {
        $options = ExtSoapOptions::defaults($wsdl, $options)->disableWsdlCache();
        $resolved = ExtSoapOptionsResolverFactory::createForWsdl($options->getWsdl())
            ->resolve($options->getOptions());

        $server = new SoapServer($options->getWsdl(), $resolved);
        $server->setObject($object);

        return new ExtSoapServerTransport($server);
    }
}
