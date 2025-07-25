<?php

namespace Mesalution\Sms\Exceptions;

use Exception;
use Throwable;

class SmsException extends Exception
{
    protected string $userMessage = 'Sms Internal Error';
    protected ?string $provider = null;
    protected array $context = [];

    public function __construct(
        string $message = "Sms Internal Error",
        string $userMessage = "Sms Internal Error",
        ?string $provider = null,
        array $context = [],
        ?ErrorCode $code = null,
        ?Throwable $previous = null
    ) {
        $provider = $provider ?? $this->provider;
        $message = sprintf($this->message, $provider, $message);
        $code = $code ? $code->value : 0;
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        $this->userMessage = $userMessage;
    }

    public function userMessage(): string
    {
        return $this->userMessage;
    }
    public function context(): array
    {
        return $this->context;
    }
    public function getProvider(): ?string
    {
        return $this->provider;
    }
}
