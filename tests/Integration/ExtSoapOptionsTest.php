<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMap;
use Soap\ExtSoapEngine\Configuration\ClassMap\ClassMapCollection;
use Soap\ExtSoapEngine\Configuration\TypeConverter;
use Soap\ExtSoapEngine\Exception\UnexpectedConfigurationException;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\ExtSoapOptionsResolverFactory;
use Soap\ExtSoapEngine\Wsdl\WsdlProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExtSoapOptionsTest extends TestCase
{
    /**
     * @var string
     */
    private $wsdl;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->wsdl = FIXTURE_DIR.'/wsdl/functional/string.wsdl';
        $this->resolver = ExtSoapOptionsResolverFactory::createForWsdl($this->wsdl);
    }

    
    public function test_it_is_possible_to_construct_from_empty_state()
    {
        $options = new ExtSoapOptions($this->wsdl, $expectedOptions = ['trace' => true]);
        static::assertSame($this->wsdl, $options->getWsdl());
        static::assertSame($expectedOptions, $options->getOptions());
    }

    
    public function test_it_contains_a_wsdl()
    {
        $wsdl = ExtSoapOptions::defaults($this->wsdl)->getWsdl();
        static::assertSame($wsdl, $this->wsdl);
    }

    
    public function test_it_can_resolve_defaults()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl, [])->getOptions()
        );

        static::assertTrue($options['trace']);
        static::assertTrue($options['exceptions']);
        static::assertSame(WSDL_CACHE_DISK, $options['cache_wsdl']);
        static::assertSame(SOAP_SINGLE_ELEMENT_ARRAYS, $options['features']);
        static::assertIsArray($options['typemap']);
    }

    
    public function test_it_is_possible_to_overwrite_defaults()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl, [
                'trace' => false,
                'proxy_host' => $proxyHost = 'http://localhost',
            ])->getOptions()
        );

        static::assertFalse($options['trace']);
        static::assertTrue($options['exceptions']);
        static::assertSame($proxyHost, $options['proxy_host']);
    }

    
    public function test_it_is_possible_to_attach_a_wsdl_provider()
    {
        $wsdlProvider = new class implements WsdlProvider {
            public function __invoke(string $wsdl): string
            {
                return 'new.wsdl';
            }
        };

        $options = ExtSoapOptions::defaults($this->wsdl, [])
            ->withWsdlProvider($wsdlProvider);

        static::assertSame('new.wsdl', $options->getWsdl());
    }

    
    public function test_it_is_possible_to_disable_wsdl_cache()
    {
        $options = $this->resolver->resolve(
            ExtSoapOptions::defaults($this->wsdl)->disableWsdlCache()->getOptions()
        );

        static::assertSame(WSDL_CACHE_NONE, $options['cache_wsdl']);
    }

    
    public function test_it_contains_a_default_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl);

        $typeMap = $options->getTypeMap();
        static::assertInstanceOf(TypeConverter\TypeConverterCollection::class, $typeMap);
        static::assertCount(4, $typeMap->getIterator());

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['typemap']);
        static::assertCount(4, $resolved['typemap']);
        static::assertSame('dateTime', $resolved['typemap'][0]['type_name']);
        static::assertSame('date', $resolved['typemap'][1]['type_name']);
        static::assertSame('decimal', $resolved['typemap'][2]['type_name']);
        static::assertSame('double', $resolved['typemap'][3]['type_name']);
    }

    
    public function test_it_is_possible_to_replace_the_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl)
            ->withTypeMap($typeMap = new TypeConverter\TypeConverterCollection());

        static::assertSame($typeMap, $options->getTypeMap());

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['typemap']);
        static::assertCount(0, $resolved['typemap']);
    }

    
    public function test_it_is_possible_to_use_regular_type_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl, [
            'typemap' => [
                [
                    'type_name' => $typeName = 'hello',
                    'type_ns' => $typeNs = 'http://my-ns/xsd',
                    'from_xml' => static function ($input) {
                        return $input;
                    },
                    'to_xml' => static function ($input) {
                        return '<xml>'.$input.'</xml>';
                    },
                ]
            ]
        ]);

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['typemap']);
        static::assertCount(1, $resolved['typemap']);
        static::assertSame($typeName, $resolved['typemap'][0]['type_name']);
        static::assertSame($typeNs, $resolved['typemap'][0]['type_ns']);

        $this->expectException(UnexpectedConfigurationException::class);
        $options->getTypeMap();
    }

    
    public function test_it_can_dynamically_add_a_default_clasmap()
    {
        $options = ExtSoapOptions::defaults($this->wsdl);

        $classMap = $options->getClassMap();
        static::assertInstanceOf(ClassMapCollection::class, $classMap);
        static::assertCount(0, $classMap->getIterator());

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['classmap']);
        static::assertCount(0, $resolved['classmap']);
    }

    
    public function test_it_is_possible_to_replace_the_class_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl)
             ->withClassMap($classMap = new ClassMapCollection(
                 new ClassMap('wsdlType', 'PhpClass')
             ));

        static::assertSame($classMap, $options->getClassMap());

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['classmap']);
        static::assertCount(1, $resolved['classmap']);
        static::assertSame('PhpClass', $resolved['classmap']['wsdlType']);
    }

    
    public function test_it_is_possible_to_use_regular_class_map()
    {
        $options = ExtSoapOptions::defaults($this->wsdl, [
            'classmap' => [
                'wsdlType' => 'PhpClass',
            ]
        ]);

        $resolved = $this->resolver->resolve($options->getOptions());
        static::assertIsArray($resolved['classmap']);
        static::assertCount(1, $resolved['classmap']);
        static::assertSame('PhpClass', $resolved['classmap']['wsdlType']);

        $this->expectException(UnexpectedConfigurationException::class);
        $options->getClassMap();
    }

    
    public function test_it_can_accept_all_knwon_options()
    {
        $options = $this->resolver->resolve(
            (new ExtSoapOptions(
                $this->wsdl,
                $expectedOptions = [
                    'uri' => 'http://localhost',
                    'location' => 'http://localhost',
                    'soap_version' => SOAP_1_1,
                    'login' => 'user',
                    'password' => 'password',
                    'authentication' => SOAP_AUTHENTICATION_BASIC,
                    'proxy_host' => 'http://proxy',
                    'proxy_port' => '8888',
                    'proxy_login' => 'proxyuser',
                    'proxy_password' => 'proxypass',
                    'local_cert' => 'somecert.key',
                    'passphrase' => 'sslpass',
                    'compression' => SOAP_COMPRESSION_GZIP,
                    'encoding' => 'utf-8',
                    'trace' => true,
                    'classmap' => [],
                    'exceptions' => true,
                    'connection_timeout' => 900,
                    'default_socket_timeout' => 900,
                    'typemap' => [],
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'user_agent' => 'My Super SoapClient',
                    'stream_context' => stream_context_create(),
                    'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'keep_alive' => false,
                    'ssl_method' => SOAP_SSL_METHOD_SSLv23,
                ]
            ))->getOptions()
        );

        foreach ($options as $key => $option) {
            static::assertSame($expectedOptions[$key], $option);
        }
    }
}
