<?php

namespace Mesalution\LaravelMesms\Tests\Feature;

use PHPUnit\Util\Test;
use Mesalution\LaravelMesms\Data\Otp;
use Mesalution\LaravelMesms\SmsManager;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\App;
use Mesalution\LaravelMesms\SmsServiceProvider;
use PHPUnit\Framework\Attributes\Group;
use Mesalution\LaravelMesms\Exceptions\OtpException;
use Mesalution\LaravelMesms\Contracts\Sms;
use Mesalution\LaravelMesms\Exceptions\VerifyOtpException;
use Mesalution\LaravelMesms\Exceptions\RequestOtpException;
use Mesalution\LaravelMesms\Providers\FakeSms;

class SmsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Spatie\LaravelData\LaravelDataServiceProvider::class,
            SmsServiceProvider::class,
        ];
    }
    protected function mockSmsDriver(string $class, callable $callback): void
    {
        $mockDriver = $this->createMock($class);
        $callback($mockDriver);

        $mockManager = $this->createMock(SmsManager::class);
        $mockManager->method('driver')->willReturn($mockDriver);
        App::instance(SmsManager::class, $mockManager);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_request_otp_success()
    {
        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('requestOTP')->willReturn(
                new Otp('otp-id-123', 'ref-code-abc')
            );
        });

        $sms = app(SmsManager::class)->driver();
        $otp = $sms->requestOTP('0812345678');
        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals('otp-id-123', $otp->id);
        $this->assertEquals('ref-code-abc', $otp->refCode);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_request_otp_throws_exception()
    {
        $this->expectException(RequestOtpException::class);

        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('requestOTP')->willThrowException(
                new RequestOtpException('mock error')
            );
        });

        $sms = app(SmsManager::class)->driver();
        $sms->requestOTP('0812345678');
    }

    #[Test]
    #[Group('sms_core')]
    public function test_resend_otp_success()
    {
        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('resendOTP')->willReturn(
                new Otp('otp-id-999', 'ref-resend-xyz')
            );
        });

        $sms = app(SmsManager::class)->driver();
        $otp = $sms->resendOTP('otp-id-999');

        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals('ref-resend-xyz', $otp->refCode);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_success()
    {
        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('verifyOTP');
        });

        $sms = app(SmsManager::class)->driver();
        $sms->verifyOTP('otp-id-123', '123456');
        $this->assertTrue(true);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_failed_by_otp_exception_with_sms()
    {
        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('verifyOTP')->willThrowException(
                new OtpException('Invalid code', 'OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง')
            );
        });

        $this->expectException(OtpException::class);
        $this->expectExceptionMessage('Invalid code');
        $sms = app(SmsManager::class)->driver();
        $otp = $sms->verifyOTP('otp-id-123', '000000');
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_failed_by_other_exception()
    {
        $this->expectException(\RuntimeException::class);

        $this->mockSmsDriver(FakeSms::class, function ($mock) {
            $mock->method('verifyOTP')->willThrowException(
                new \RuntimeException('Something went wrong')
            );
        });

        $sms = app(SmsManager::class)->driver();
        $sms->verifyOTP('otp-id-123', '123456');
    }
}
