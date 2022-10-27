# Ext-SOAP powered SOAP engine

This package is a [SOAP engine](https://github.com/php-soap/engine) that leverages the built-in functions from  PHP's `ext-soap` extension.

It basically flips the `SoapClient` inside out: All the built-in functions for encoding, decoding and HTTP transport can be used in a standalone way.

If your package contains a `SoapClient`, you might consider using this package as an alternative:

* It gives you full control over the HTTP layer.
* It validates the `$options` you pass to the `SoapClient` and gives you meaningful errors.
* It transforms the types and methods into real objects so that you can actually use that information.
* It makes it possible to use the encoding / decoding logic without doing any SOAP calls to a server.
* ...

# Want to help out? 💚

- [Become a Sponsor](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#sponsor)
- [Let us do your implementation](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#let-us-do-your-implementation)
- [Contribute](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#contribute)
- [Help maintain these packages](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#maintain)

Want more information about the future of this project? Check out this list of the [next big projects](https://github.com/php-soap/.github/blob/main/PROJECTS.md) we'll be working on.

# Installation

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
Therefore we recommend using the [PSR-18 based transport](https://github.com/php-soap/psr18-transport/) instead of the ones above.
Besides dealing with some issues, it also provides a set of middleware for dealing with some common issues you might not be able to solve with the regular SoapClient.


## Configuration options

### ExtSoapOptions

This package provides a little wrapper around all available `\SoapClient` [options](https://www.php.net/manual/en/soapclient.construct.php).
It provides sensible default options. If you want to set specific options, you can do so in a sane way:
It will validate the options before they are passed to the `\SoapClient`.
This way, you'll spend less time browsing the official PHP documentation.

```php
<?php

use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;use Soap\ExtSoapEngine\Wsdl\TemporaryWsdlLoaderProvider;
use Soap\Psr18Transport\Wsdl\Psr18Loader;
use Soap\Wsdl\Loader\FlatteningLoader;

$options = ExtSoapOptions::defaults($wsdl, ['location' => 'http://somedifferentserver.com'])
    ->disableWsdlCache()
    ->withClassMap(\MyClassMap::getCollection())
    ->withWsdlProvider(new TemporaryWsdlLoaderProvider(
        new FlatteningLoader(new Psr18Loader($httpClient)),
        new Md5Strategy(),
        'some/dir'
    ));

$typemap = $options->getTypeMap();
$typemap->add(new \MyTypeConverter());
```

### WsdlProvider

A WSDL provider can be used in order to load a WSDL.
Since ext-soap requires a loadable URL, it works slightly different from the [wsdl loaders inside php-soap/wsdl](https://github.com/php-soap/wsdl#wsdl-loader).

```php
use Soap\ExtSoapEngine\ExtSoapOptions;

$options = ExtSoapOptions::defaults($wsdl)
    ->withWsdlProvider($yourProvider);
```

This package contains some built-in providers:

#### InMemoryWsdlProvider

By using the in-memory WSDL provider, you can use a complete XML version of the WSDL as source.
This one might come in handy during tests, but probably shouldn't be used in production.

```php
<?php
use Soap\ExtSoapEngine\Wsdl\InMemoryWsdlProvider;

$provider = new InMemoryWsdlProvider();
$wsdl = ($provider)('<definitions ..... />');
```

#### PassThroughWsdlProvider

The pass-through WSDL provider is used by default.
You can pass every string you would normally pass to the built-in SOAP client's wsdl option.
No additional checks are executed, the loading of the file will be handled by the internal `SoapClient` class.

```php
<?php
use Soap\ExtSoapEngine\Wsdl\PassThroughWsdlProvider;

$provider = new PassThroughWsdlProvider();
$wsdl = ($provider)('some.wsdl');
```

#### PermanentWsdlLoaderProvider

This provider can permanently cache a (remote) WSDL.
This one is very useful to use in production, where the WSDL shouldn't change too much.
You can force it to load to a permanent location in e.g. a cronjob.
It will improve performance since the soap-client won't have to fetch the WSDL remotely.
You can use any [WSDL loader](https://github.com/php-soap/wsdl#wsdl-loader)

```php
<?php
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;
use Soap\ExtSoapEngine\Wsdl\PermanentWsdlLoaderProvider;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;

$provider = new PermanentWsdlLoaderProvider(
    new FlatteningLoader(new StreamWrapperLoader()),
    new Md5Strategy(),
    'target/location'
);

// Force downloads:
$provider = $provider->forceDownload();

$wsdl = ($provider)('some.wsdl');
```

#### TemporaryWsdlLoaderProvider

This provider can temporarily fetch a (remote) WSDL through a WSDL loader.
This one can be used in development, where WSDL files might change frequently.
You can use any [WSDL loader](https://github.com/php-soap/wsdl#wsdl-loader)

```php
<?php
use Soap\ExtSoapEngine\Wsdl\Naming\Md5Strategy;
use Soap\ExtSoapEngine\Wsdl\TemporaryWsdlLoaderProvider;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;

$provider = new TemporaryWsdlLoaderProvider(
    new FlatteningLoader(new StreamWrapperLoader()),
    new Md5Strategy(),
    'target/location'
);

$wsdl = ($provider)('some.wsdl');
```

#### Writing your own WSDL provider

Didn't find the WSDL provider you needed?
No worries! It is very easy to create your own WSDL provider. The only thing you'll need to do is implement the WsdlProviderInterface:

```php
namespace Soap\ExtSoapEngine\Wsdl;

interface WsdlProvider
{
    /**
     * This method can be used to transform a location into another location.
     * The output needs to be processable by the SoapClient $wsdl option.
     */
    public function __invoke(string $location): string;
}
```

### ClassMap

By providing a class map, you let `ext-soap` know how data of specific SOAP types can be converted to actual classes.

**Usage:**

```php
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\ExtSoapOptions;

$options = ExtSoapOptions::defaults($wsdl);
$classmap = $options->getClassMap();
$classmap->set(new ClassMap('WsdlType', 'PhpClassName'));
```

### TypeConverter

Some exotic XSD types are hard to transform to PHP objects.
A typical example are dates: some people like it as a timestamp, some want it as a DateTime, ...
By adding custom TypeConverters, it is possible to convert a WSDL type to / from a PHP type.

These TypeConverters are added by default:

- DateTimeTypeConverter
- DateTypeConverter
- DoubleTypeConverter
- DecimalTypeConverter

You can also create your own converter by implementing the `TypeConverterInterface`.

**Usage:**

```php
use Soap\ExtSoapEngine\Configuration\TypeConverter;
use Soap\ExtSoapEngine\ExtSoapOptions;

$options = ExtSoapOptions::defaults($wsdl);
$typemap = $options->getTypeMap();
$typemap->add(new TypeCOnverter\DateTimeTypeConverter());
$typemap->add(new TypeConverter\DecimalTypeConverter());
$typemap->add(new TypeConverter\DoubleTypeConverter());
```

