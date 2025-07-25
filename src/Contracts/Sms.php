<?php

namespace Mesalution\LaravelMesms\Contracts;

use Mesalution\LaravelMesms\Data\Otp;

interface Sms
{
    public function requestOTP(string $mobileNo): Otp;
    public function resendOTP(string $otpId): Otp;
    public function verifyOTP(string $otpId, string $otpCode): void;
}
