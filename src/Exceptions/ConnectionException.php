<?php

namespace Mesalution\Sms\Exceptions;

class ConnectionException extends SmsException
{
    protected $message = "Unable to connect to provider [%s]: %s";
    protected string $userMessage = "ไม่สามารถเชื่อมต่อกับผู้ให้บริการ SMS ได้";
}
