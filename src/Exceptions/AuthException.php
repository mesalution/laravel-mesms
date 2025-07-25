<?php

namespace Mesalution\LaravelMesms\Exceptions;

class AuthException extends SmsException
{
    protected $message = "Authentication failed [%s]: %s";
    protected string $userMessage = "ไม่สามารถยืนยันตนในการใช้งาน API ของผู้ให้บริการ SMS ได้";
}
