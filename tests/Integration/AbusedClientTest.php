<?php

declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Integration;

use PHPUnit\Framework\TestCase;
use Soap\Engine\HttpBinding\SoapResponse;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapMetadata;
use Soap\ExtSoapEngine\Generator\DummyMethodArgumentsGenerator;

final class AbusedClientTest extends TestCase
{
    /**
     * @var AbusedClient
     */
    private $client;

    protected function configureForWsdl(string $wsdl, array $options)
    {
        $this->client = new AbusedClient($wsdl, $options);
    }

    
    public function test_it_can_encode_with_typemap()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->__soapCall('validate', ['goodbye']);
        $encoded = $this->client->collectRequest();

        static::assertStringContainsString('hello', $encoded->getRequest());
        static::assertStringNotContainsString('goodbye', $encoded->getRequest());
    }

    
    public function test_it_can_decode_with_typemap()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->registerResponse($this->generateSoapResponse(
            <<<EOB
<application:validate>
    <output xsi:type="xsd:string">goodbye</output>
</application:validate>
EOB
        ));

        $metadata = new ExtSoapMetadata($this->client);
        $payload = (new DummyMethodArgumentsGenerator($metadata))->generateForSoapCall('validate');
        $decoded = $this->client->__soapCall('validate', $payload);

        static::assertSame('hello', $decoded);
    }

    
    public function test_it_can_decode_with_more_complex_types()
    {
        $this->configureForWsdl(FIXTURE_DIR.'/wsdl/functional/string.wsdl', [
            'typemap' => $this->generateHelloTypeMap('string'),
        ]);

        $this->client->registerResponse($this->generateSoapResponse(
            <<<EOB
<application:validate>
    <response xsi:type="application:ValidateResponse">
        <output xsi:type="xsd:string">goodbye</output>
    </response>
</application:validate>
EOB
        ));

        $metadata = new ExtSoapMetadata($this->client);
        $payload = (new DummyMethodArgumentsGenerator($metadata))->generateForSoapCall('validate');
        $decoded = $this->client->__soapCall('validate', $payload);

        static::assertSame('hello', $decoded);
    }

    private function generateSoapResponse(string $body): SoapResponse
    {
        $response = <<<EORESPONSE
<SOAP-ENV:Envelope
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
    xmlns:application="http://soapinterop.org/"
    xmlns:s="http://soapinterop.org/xsd"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        $body
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EORESPONSE;

        return new SoapResponse($response);
    }

    private function generateHelloTypeMap(string $xsdType): array
    {
        return [
            [
                'type_name' => $xsdType,
                'type_ns'   => 'http://www.w3.org/2001/XMLSchema',
                'from_xml'  => static function () {
                    return 'hello';
                },
                'to_xml'    => static function () {
                    return '<d>hello</d>';
                },
            ],
        ];
    }
}
