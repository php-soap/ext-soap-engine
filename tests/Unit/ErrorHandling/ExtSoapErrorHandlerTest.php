<?php
declare(strict_types=1);

namespace SoapTest\ExtSoapEngine\Unit\ErrorHandling;

use PHPUnit\Framework\TestCase;
use Soap\ExtSoapEngine\ErrorHandling\ExtSoapErrorHandler;
use Soap\ExtSoapEngine\Exception\RequestException;

final class ExtSoapErrorHandlerTest extends TestCase
{
    public function test_it_can_deal_with_null_responses(): void
    {
        $this->expectException(RequestException::class);

        ExtSoapErrorHandler::handleNullResponse(null);
    }

    public function test_it_can_deal_with_non_null_responses(): void
    {
        $res = ExtSoapErrorHandler::handleNullResponse('hello');

        static::assertSame('hello', $res);
    }

    public function test_it_can_detect_no_internal_errors_during_callback(): void
    {
        $res = ExtSoapErrorHandler::handleInternalErrors(
            static fn () => 'hello'
        );

        static::assertSame($res, 'hello');
    }

    public function test_it_can_detect_internal_errors_during_callback(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('hello');

        ExtSoapErrorHandler::handleInternalErrors(
            static function () {
                trigger_error('hello');
                return 'x';
            }
        );
    }
}
