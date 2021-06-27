# Ext-SOAP powered SOAP engine

This package is a [SOAP engine](https://github.com/php-soap/engine) that leverages the built-in functions from  PHP's `ext-soap` extension.

It basically flips the `SoapClient` inside out: All the built-in functions for encoding, decoding and HTTP transport can be used in a standalone way.

If your package contains a `SoapClient`, you might consider using this package as an alternative:

* It gives you full control over the HTTP layer.
* It validates the `$options` you pass to the `SoapClient` and gives you meaningful errors.
* It transforms the types and methods into real objects so that you can actually use that information.
* It makes it possible to use the encoding / decoding logic without doing any SOAP calls to a server.
* ...

## Installation

```shell
composer install php-soap/ext-soap-engine
```

## Example usage:

This example contains an advanced setup for creating a flexible ext-soap based engine.
It shows you the main components that you can use for configuring PHP's `SoapClient` and to transform it into a SOAP engine:

```php
use Soap\Engine\SimpleEngine;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\Configuration\TypeConverter\TypeConverterCollection;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Transport\ExtSoapClientTransport;
use Soap\ExtSoapEngine\Transport\TraceableTransport;

$engine = new SimpleEngine(
    ExtSoapDriver::createFromClient(
        $client = AbusedClient::createFromOptions(
            ExtSoapOptions::defaults($wsdl, [
                'soap_version' => SOAP_1_2,
            ])
                ->disableWsdlCache()
                ->withClassMap(new ClassMapCollection())
                ->withTypeMap(new TypeConverterCollection())
        )
    ),
    $transport = new TraceableTransport(
        $client,
        new ExtSoapClientTransport($client)
    )
);
```

Fetching a SOAP Resource:

```php
$result = $engine->request('SomeMethod', [(object)['param1' => true]]);

// Collecting last soap call:
var_dump($transport->collectLastRequestInfo());
```

You can still set advanced configuration on the actual SOAP client:

```php
$client->__setLocation(...);
$client->__setSoapHeaders(...);
$client->__setCookie(...);
```

Reading / Parsing metadata

```php
var_dump(
    $engine->getMetadata()->getMethods(),
    $engine->getMetadata()->getTypes()
);

$methodInfo = $engine->getMetadata()->getMethods()->fetchByName('SomeMethod');
```

## Engine

This package provides following engine components:

* **ExtSoapEncoder:** Uses PHP's `SoapClient` in order to encode a mixed request body into a SOAP request.
* **ExtSoapDecoder:** Uses PHP's `SoapClient` in order to decode a SOAP Response into mixed data.
* **ExtSoapMetadata:** Parses the methods and types from PHP's `SoapClient` into something more usable.
* **ExtSoapDriver:** Combines the ext-soap encoder, decoder and metadata tools into a usable `ext-soap` preset.

### Transports

* **ExtSoapClientTransport:** Uses PHP's `SoapClient` to handle SOAP requests.
* **ExtSoapServerTransport:** Uses PHP's `SoapServer` to handle SOAP requests. It can e.g. be used during Unit tests.
* **TraceableTransport:** Can be used to decorate another transport and keeps track of the last request and response. It should be used as an alternative for fetching it on the SoapClient.

In ext-soap, there are some well known issues regarding the HTTP layer.
Therefore we recommend using the [PSR-18 based transport](https://github.com/php-soap/psr18-transport/) instead of the once above.
Besides dealing with some issues, it also provides a set of middleware for dealing with some common issues you might not be able to solve with the regular SoapClient.


## Configuration options

TODO: 

- ExtSoapOptions : https://github.com/phpro/soap-client/blob/master/docs/drivers/ext-soap.md#extsoapoptions
- WsdlProvider : https://github.com/phpro/soap-client/blob/master/docs/wsdl-providers.md
- ClassMap
- TypeConverter: https://github.com/phpro/soap-client/blob/master/docs/type-converter.md

