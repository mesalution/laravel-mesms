<?php

namespace Mesalution\Sms\Exceptions;

class ExpiredOtpException extends OtpException
{
    protected $message = "OTP is expired [%s]";
    protected string $userMessage = "OTP หมดอายุไปแล้ว กรุณาขอใหม่อีกครั้ง";
}
