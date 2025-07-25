<?php

namespace Mesalution\Sms\Providers;

use Illuminate\Support\Str;
use Mesalution\Sms\Data\Otp;
use Mesalution\Sms\Contracts\Sms;

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
