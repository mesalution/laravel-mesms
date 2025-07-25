<?php

namespace Mesalution\LaravelMesms;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Mesalution\LaravelMesms\Providers\FakeSms;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/sms.php', 'sms');

        $this->app->singleton(SmsManager::class, fn() => new SmsManager());
        $this->app->bind(Sms::class, function (Application $app) {
            if ($app->environment('production') || $app->environment('staging')) {
                return $app->make(SmsManager::class)->driver();
            }
            return $app->make(SmsManager::class)->driver('fake');
        });
    }
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/sms.php' => config_path('sms.php'),
        ]);
    }
}
