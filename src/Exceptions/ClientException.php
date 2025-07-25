<?php

namespace Mesalution\LaravelMesms\Exceptions;

class ClientException extends SmsException
{
    protected $message = "Send bad request to provider [%s]: %s";
    protected string $userMessage = "ส่งข้อมูลไปยังผู้ให้บริการ SMS ไม่ถูกต้อง";
}
