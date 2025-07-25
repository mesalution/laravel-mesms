<?php

namespace Mesalution\Sms\Tests\Feature;

use PHPUnit\Util\Test;
use Mesalution\Sms\Sms;
use Mesalution\Sms\Data\Otp;
use Mesalution\Sms\SmsManager;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\App;
use Mesalution\Sms\SmsServiceProvider;
use PHPUnit\Framework\Attributes\Group;
use Mesalution\Sms\Exceptions\OtpException;
use Mesalution\Sms\Contracts\Sms as SmsInterface;
use Mesalution\Sms\Exceptions\VerifyOtpException;
use Mesalution\Sms\Exceptions\RequestOtpException;

class SmsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }
    protected function mockSmsDriver(callable $callback): void
    {
        $mockDriver = $this->createMock(SmsInterface::class);
        $callback($mockDriver);

        $mockManager = $this->createMock(SmsManager::class);
        $mockManager->method('driver')->willReturn($mockDriver);
        App::instance(SmsManager::class, $mockManager);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_request_otp_success()
    {
        $this->mockSmsDriver(function ($mock) {
            $mock->method('requestOTP')->willReturn(
                new Otp('otp-id-123', 'ref-code-abc')
            );
        });

        $sms = new Sms('mock');
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

        $this->mockSmsDriver(function ($mock) {
            $mock->method('requestOTP')->willThrowException(
                new RequestOtpException('mock error')
            );
        });

        $sms = new Sms('mock');
        $sms->requestOTP('0812345678');
    }

    #[Test]
    #[Group('sms_core')]
    public function test_resend_otp_success()
    {
        $this->mockSmsDriver(function ($mock) {
            $mock->method('resendOTP')->willReturn(
                new Otp('otp-id-999', 'ref-resend-xyz')
            );
        });

        $sms = new Sms('mock');
        $otp = $sms->resendOTP('otp-id-999');

        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals('ref-resend-xyz', $otp->refCode);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_success()
    {
        $this->mockSmsDriver(function ($mock) {
            $mock->method('verifyOTP');
        });

        $sms = new Sms('mock');
        $otp = $sms->verifyOTP('otp-id-123', '123456');

        $this->assertTrue($otp->result);
        $this->assertEquals('OTP verified successfully', $otp->message);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_failed_by_otp_exception()
    {
        $this->mockSmsDriver(function ($mock) {
            $mock->method('verifyOTP')->willThrowException(
                new OtpException('Invalid code', 'OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง')
            );
        });

        $sms = new Sms('mock');
        $otp = $sms->verifyOTP('otp-id-123', '000000');
        $this->assertFalse($otp->result);
        $this->assertEquals('OTP ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง', $otp->message);
    }

    #[Test]
    #[Group('sms_core')]
    public function test_verify_otp_failed_by_other_exception()
    {
        $this->expectException(VerifyOtpException::class);

        $this->mockSmsDriver(function ($mock) {
            $mock->method('verifyOTP')->willThrowException(
                new \RuntimeException('Something went wrong')
            );
        });

        $sms = new Sms('mock');
        $sms->verifyOTP('otp-id-123', '123456');
    }
}
