<?php

namespace Mesalution\Sms\Data\Promotech;

use Spatie\LaravelData\Data;

class Success extends Data
{
    public function __construct(
        public string $message,
        public string $description,
    ) {}
}
