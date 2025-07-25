<?php

namespace Mesalution\LaravelMesms\Data;

use Spatie\LaravelData\Data;

class Otp extends Data
{
    public function __construct(
        public ?string $id = null,
        public ?string $refCode = null,
        public ?string $expiredAt = null,
        public ?bool $result = null,
        public ?string $message = null,
    ) {}
}
