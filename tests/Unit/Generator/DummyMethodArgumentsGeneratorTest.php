<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\ParameterCollection;
use Soap\Engine\Metadata\LazyInMemoryMetadata;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Parameter;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\ExtSoapEngine\Generator\DummyMethodArgumentsGenerator;

class DummyMethodArgumentsGeneratorTest extends TestCase
{
    function test_it_can_parse_dummy_arguments()
    {
        $meta = $this->createConfiguredMock(Metadata::class, [
            'getMethods' => new MethodCollection(
                new Method(
                    'method',
                    new ParameterCollection(
                        new Parameter('param1', XsdType::create('string')),
                        new Parameter('param1', XsdType::create('integer')),
                    ),
                    XsdType::create('string')
                )
            )
        ]);
        $generator = new DummyMethodArgumentsGenerator($meta);

        $actual = $generator->generateForSoapCall('method');
        self::assertSame([null, null], $actual);
    }
}