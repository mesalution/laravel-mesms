<?php

namespace Mesalution\LaravelMesms\Providers;

use Illuminate\Support\Str;
use Mesalution\LaravelMesms\Data\Otp;
use Mesalution\LaravelMesms\Contracts\Sms;
use Mesalution\LaravelMesms\Exceptions\InvalidOtpException;

class FakeSms implements Sms
{
    protected string $validOtp = '123456';
    public function requestOTP(string $mobileNo): Otp
    {
        $otp = new Otp(
            Str::random(),
            "OTP Code Is: " . $this->validOtp,
        );
        return $otp;
    }
    public function resendOTP(string $otpId): Otp
    {
        $otp = new Otp(
            Str::random(),
            "OTP Code Is: " . $this->validOtp,
        );
        return $otp;
    }
    public function verifyOTP(string $otpId, string $otpCode): void
    {
        if ($otpCode != $this->validOtp) {
            throw new InvalidOtpException(
                provider: class_basename($this),
                context: [
                    'otpId' => $otpId,
                    'otpCode' => $otpCode,
                    'verifyData' => [],
                ]
            );
        }
    }
}
