<?php

namespace Mesalution\Sms\Exceptions;

class VerifyOtpException extends SmsException
{
    protected $message = "Verify otp failed [%s]: %s";
    protected string $userMessage = "ตรวจสอบ OTP ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
}
