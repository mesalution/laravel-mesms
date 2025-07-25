<?php

namespace Mesalution\Sms;

use Mesalution\Sms\Contracts\Sms;
use Mesalution\Sms\Exceptions\SmsException;

class SmsManager
{
    public function __construct(
        protected array $config = []
    ) {
        if (empty($this->config)) {
            $this->config = config('sms');
        }
    }

    public function driver(?string $driver = null, ?array $customOptions = null): Sms
    {
        $driver = $driver ?? $this->config['driver'] ?? null;
        if (!$driver) {
            throw new SmsException('Driver not found');
        }
        $providers = $this->config['providers'] ?? [];
        $driverConfig = $providers[$driver] ?? null;
        if (!$driverConfig || empty($driverConfig['class'])) {
            throw new SmsException('Class of driver not found');
        }
        $class = $driverConfig['class'];
        if (!class_exists($class)) {
            throw new SmsException("Class '{$class}' of driver '{$driver}' not found");
        }
        $options = $driverConfig['options'] ?? null;
        if (!$options) {
            throw new SmsException("Option of driver '{$driver}' not found");
        }
        if ($customOptions) {
            $options = $customOptions;
        }
        return new $class($options);
    }
}
