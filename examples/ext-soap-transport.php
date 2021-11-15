<?php

use Soap\Engine\SimpleEngine;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapDriver;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Soap\ExtSoapEngine\Transport\ExtSoapClientTransport;
use Soap\ExtSoapEngine\Transport\TraceableTransport;

require_once dirname(__DIR__).'/vendor/autoload.php';

$engine = new SimpleEngine(
    ExtSoapDriver::createFromClient(
        $client = AbusedClient::createFromOptions(
            ExtSoapOptions::defaults('http://www.dneonline.com/calculator.asmx?wsdl', [
                'connection_timeout' => 0,
            ])
        )
    ),
    $transport = new TraceableTransport(
        $client,
        new ExtSoapClientTransport($client)
    )
);

$result = $engine->request('Add', [['intA' => 1, 'intB' => 2]]);

var_dump($result);
var_dump($transport->collectLastRequestInfo());
