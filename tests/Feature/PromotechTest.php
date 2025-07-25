<?php

namespace Mesalution\LaravelMesms\Tests\Feature;

use Mesalution\LaravelMesms\Data\Otp;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Mesalution\LaravelMesms\Providers\Promotech;
use PHPUnit\Framework\Attributes\Group;
use Orchestra\Testbench\TestCase;
use Mesalution\LaravelMesms\Exceptions\InternalException;
use Mesalution\LaravelMesms\Exceptions\ConnectionException;
use Mesalution\LaravelMesms\Exceptions\BadResponseException;
use Mesalution\LaravelMesms\Exceptions\ErrorCode;
use Mesalution\LaravelMesms\Exceptions\ExternalException;
use Mesalution\LaravelMesms\Exceptions\OtpException;

class PromotechTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Spatie\LaravelData\LaravelDataServiceProvider::class,
        ];
    }
    protected function makeService(array $options = []): Promotech
    {
        return new Promotech(array_merge([
            'url' => 'https://fake-api.test',
            'username' => 'fakeuser',
            'password' => 'fakepass',
            'otcId' => 'FAKE_OTCID',
        ], $options));
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_request_otp_successfully()
    {
        $mockResponse = [
            'success' => [
                'message' => 'Success',
                'description' => 'Success',
            ],
            'otcId' => 'otc-id-test',
            'otpId' => '123456',
            'referrenceCode' => 'ABCDEF'
        ];
        Http::fake([
            'https://fake-api.test/otp/requestOTP' => Http::response($mockResponse, 200),
        ]);

        $service = $this->makeService();
        $otp = $service->requestOTP('0812345678');

        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals('123456', $otp->id);
        $this->assertEquals('ABCDEF', $otp->refCode);
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_request_otp_should_throws_bad_response_when_missing_fields()
    {
        Http::fake([
            '*' => Http::response([
                'otpId' => 'no_success_flag'
            ], 200),
        ]);

        $this->expectException(BadResponseException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_BAD_RESPONSE->value);
        $service = $this->makeService();
        $service->requestOTP('0812345678');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_request_otp_should_throws_bad_response_when_cannot_create_data()
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'otpId' => null, // สมมุติว่าทำให้ from() สร้าง data ไม่ได้
                'referrenceCode' => null
            ], 200),
        ]);

        $this->expectException(BadResponseException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_BAD_RESPONSE->value);
        $service = $this->makeService();
        $service->requestOTP('0812345678');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_request_otp_should_throws_connection_exception_on_network_failure()
    {
        Http::fake([
            '*' => fn() => throw new \Illuminate\Http\Client\ConnectionException("Connection failed"),
        ]);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_CONNECTION_REFUSED->value);
        $service = $this->makeService();
        $service->requestOTP('0812345678');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_request_otp_should_throws_internal_exception_on_other_errors()
    {
        Http::fake([
            '*' => fn() => throw new \RuntimeException("Random failure"),
        ]);

        $this->expectException(InternalException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_INTERNAL_ERROR->value);
        $service = $this->makeService();
        $service->requestOTP('0812345678');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_resend_otp_successfully()
    {
        $mockResponse = [
            'success' => [
                'message' => 'Success',
                'description' => 'Success',
            ],
            'otcId' => 'otc-id-test',
            'otpId' => '123456',
            'referrenceCode' => 'ABCDEF'
        ];
        Http::fake([
            'https://fake-api.test/otp/resendOTP' => Http::response($mockResponse, 200),
        ]);

        $service = $this->makeService();
        $otp = $service->resendOTP($mockResponse['otpId']);

        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals('123456', $otp->id);
        $this->assertEquals('ABCDEF', $otp->refCode);
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_resend_otp_should_throws_bad_response_when_missing_fields()
    {
        Http::fake([
            '*' => Http::response([
                'otpId' => 'no_success_flag'
            ], 200),
        ]);

        $this->expectException(BadResponseException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_BAD_RESPONSE->value);
        $service = $this->makeService();
        $service->resendOTP('otp-id-test');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_resend_otp_should_throws_bad_response_when_cannot_create_data()
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'otpId' => null, // สมมุติว่าทำให้ from() สร้าง data ไม่ได้
                'referrenceCode' => null
            ], 200),
        ]);

        $this->expectException(BadResponseException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_BAD_RESPONSE->value);
        $service = $this->makeService();
        $service->resendOTP('otp-id-test');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_resend_otp_should_throws_connection_exception_on_network_failure()
    {
        Http::fake([
            '*' => fn() => throw new \Illuminate\Http\Client\ConnectionException("Connection failed"),
        ]);

        $this->expectException(ConnectionException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_CONNECTION_REFUSED->value);
        $service = $this->makeService();
        $service->resendOTP('otp-id-test');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_resend_otp_should_throws_internal_exception_on_other_errors()
    {
        Http::fake([
            '*' => fn() => throw new \RuntimeException("Random failure"),
        ]);

        $this->expectException(InternalException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_INTERNAL_ERROR->value);
        $service = $this->makeService();
        $service->resendOTP('otp-id-test');
    }

    #[Test]
    #[Group('promotech_gateway')]
    public function test_verify_otp_successfully()
    {
        $mockResponse = [
            'success' => [
                'message' => 'Success',
                'description' => 'Success',
            ],
            'otpId' => 'otp-id-test',
            'result' => true,
            'isErrorCount' => false,
            'isExprCode' => false,
        ];
        Http::fake([
            'https://fake-api.test/otp/verifyOTP' => Http::response($mockResponse, 200),
        ]);

        $service = $this->makeService();
        $service->verifyOTP($mockResponse['otpId'], '123456');
        $this->assertTrue(true);
    }
    #[Test]
    #[Group('promotech_gateway')]
    public function test_verify_otp_with_invalid_otp_should_throw_error()
    {
        $mockResponse = [
            'success' => [
                'message' => 'Success',
                'description' => 'Success',
            ],
            'otpId' => 'otp-id-test',
            'result' => false,
            'isErrorCount' => true,
            'isExprCode' => false,
        ];
        Http::fake([
            'https://fake-api.test/otp/verifyOTP' => Http::response($mockResponse, 200),
        ]);

        $this->expectException(OtpException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_INVALID_OTP_CODE->value);
        $service = $this->makeService();
        $service->verifyOTP($mockResponse['otpId'], '123456');
    }
    #[Test]
    #[Group('promotech_gateway')]
    public function test_verify_otp_with_otp_expired_should_throw_error()
    {
        $mockResponse = [
            'success' => [
                'message' => 'Success',
                'description' => 'Success',
            ],
            'otpId' => 'otp-id-test',
            'result' => false,
            'isErrorCount' => false,
            'isExprCode' => true,
        ];
        Http::fake([
            'https://fake-api.test/otp/verifyOTP' => Http::response($mockResponse, 200),
        ]);

        $this->expectException(OtpException::class);
        $this->expectExceptionCode(ErrorCode::PROMOTECH_OTP_EXPIRED->value);
        $service = $this->makeService();
        $service->verifyOTP($mockResponse['otpId'], '123456');
    }
}
