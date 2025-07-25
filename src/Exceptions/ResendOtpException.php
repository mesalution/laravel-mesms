<?php

namespace Mesalution\Sms\Exceptions;

class ResendOtpException extends SmsException
{
    protected $message = "Resend otp failed [%s]: %s";
    protected string $userMessage = "ขอ OTP อีกครั้งไม่สำเร็จ กรุณาทำรายการใหม่ในภายหลัง";
}
