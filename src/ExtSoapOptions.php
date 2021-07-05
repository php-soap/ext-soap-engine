<?php

declare(strict_types=1);

namespace Soap\ExtSoapEngine;

use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\Configuration\TypeConverter;
use Soap\ExtSoapEngine\Exception\UnexpectedConfigurationException;
use Soap\ExtSoapEngine\Configuration\TypeConverter\TypeConverterCollection;
use Soap\ExtSoapEngine\Wsdl\PassThroughWsdlProvider;
use Soap\ExtSoapEngine\Wsdl\WsdlProvider;

final class ExtSoapOptions
{
    private string $wsdl;
    private array $options;
    private WsdlProvider $wsdlProvider;

    public function __construct(string $wsdl, array $options = [])
    {
        $this->wsdl = $wsdl;
        $this->options = $options;
        $this->wsdlProvider = new PassThroughWsdlProvider();
    }

    public static function defaults(string $wsdl, array $options = []): self
    {
        return (
            new self(
                $wsdl,
                array_merge(
                    [
                        'trace' => true,
                        'exceptions' => true,
                        'keep_alive' => true,
                        'cache_wsdl' => WSDL_CACHE_DISK, // Avoid memory cache: this causes SegFaults from time to time.
                        'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                        'typemap' => new TypeConverterCollection([
                            new TypeConverter\DateTimeTypeConverter(),
                            new TypeConverter\DateTypeConverter(),
                            new TypeConverter\DecimalTypeConverter(),
                            new TypeConverter\DoubleTypeConverter()
                        ]),
                    ],
                    $options
                )
            )
        );
    }

    public function getWsdl(): string
    {
        return ($this->wsdlProvider)($this->wsdl);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function withWsdlProvider(WsdlProvider $wsdlProvider): self
    {
        $this->wsdlProvider = $wsdlProvider;

        return $this;
    }

    public function getClassMap(): ClassMapCollection
    {
        return $this->fetchOptionOfTypeWithDefault(
            'classmap',
            ClassMapCollection::class,
            new ClassMapCollection()
        );
    }

    public function withClassMap(ClassMapCollection $classMapCollection): self
    {
        $this->options['classmap'] = $classMapCollection;

        return $this;
    }

    public function getTypeMap(): TypeConverterCollection
    {
        return $this->fetchOptionOfTypeWithDefault(
            'typemap',
            TypeConverterCollection::class,
            new TypeConverterCollection()
        );
    }

    public function withTypeMap(TypeConverterCollection $typeConverterCollection): self
    {
        $this->options['typemap'] = $typeConverterCollection;

        return $this;
    }

    public function disableWsdlCache(): self
    {
        $this->options['cache_wsdl'] = WSDL_CACHE_NONE;

        return $this;
    }

    private function fetchOptionOfTypeWithDefault(string $key, string $type, $default)
    {
        $this->options[$key] = $this->options[$key] ?? $default;

        if (!$this->options[$key] instanceof $type) {
            throw UnexpectedConfigurationException::expectedTypeButGot($key, $type, $this->options[$key]);
        }

        return $this->options[$key];
    }
}
