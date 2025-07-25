<?php

namespace Mesalution\LaravelMesms\Providers;

use Illuminate\Support\Str;
use Mesalution\LaravelMesms\Data\Otp;
use Mesalution\LaravelMesms\Contracts\Sms;

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
}
