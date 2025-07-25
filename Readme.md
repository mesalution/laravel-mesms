# Mesalution - Laravel SMS

[![Latest Version](https://img.shields.io/github/v/tag/mesalution/laravel-mesms?label=version&sort=semver)](https://github.com/mesalution/laravel-mesms/releases)
[![License](https://img.shields.io/github/license/mesalution/laravel-mesms)](LICENSE)


แพ็กเกจ Laravel สำหรับส่งและจัดการ OTP ผ่านผู้ให้บริการ SMS ได้หลายเจ้า รองรับการเปลี่ยน driver ได้ง่าย และรองรับการเขียน fake สำหรับการทดสอบ

## Feature

- รองรับการขอ OTP / ยืนยัน OTP / ขอ OTP ซ้ำ
- สลับ driver ได้ตาม environment (เช่น Production ใช้ gateway จริง, Local ใช้ Fake)
- ออกแบบตามหลัก SOLID ใช้งานร่วมกับ Laravel ได้เต็มรูปแบบ
- รองรับการจัดการ error ผ่าน Exception เฉพาะทาง

---

## Spec Requirement
- PHP >= 8.2
- Laravel >= 10
- Support `Illuminate\Support\Facades\App`
- Recommend use with Laravel Service Container

## Installation

```bash
composer require mesalution/laravel-mesms
```

## Configuration
Publish the config:

```bash
php artisan vendor:publish --provider="Mesalution\Sms\SmsServiceProvider"
```
Edit `config/sms.php`:
```bash
return [
    'driver' => env('SMS_DRIVER', 'promotech'),
    'providers' => [
        'fake' => [
            'class' => \Mesalution\Sms\Providers\FakeSms::class,
            'options' => []
        ],
        'promotech' => [
            'class' => \Mesalution\Sms\Providers\Promotech::class,
            'options' => [
                'url' => env('PROMOTECH_URL', 'http://apisms.promotech.co.th'),
                'username' => env('PROMOTECH_USERNAME'),
                'password' => env('PROMOTECH_PASSWORD'),
                'otcId' => env('PROMOTECH_OTC_ID'),
                'senderName' => env('PROMOTECH_SENDER_NAME'),
            ],
        ]
    ]
];

```
Or config in `.env`
```bash
SMS_DRIVER=promotech
PROMOTECH_USERNAME={{username}}
PROMOTECH_PASSWORD={{password}}
PROMOTECH_OTC_ID={{otcId}}
PROMOTECH_SENDER_NAME={{senderName}}
```

## Usage
Create instance from `app()` helper
```bash
use Mesalution\Sms\Sms;

$sms = app(Sms::class);

$otp = $sms->requestOTP('0812345678');

$sms->verifyOTP('otp-id-xxx', '123456');
$sms->resendOTP('otp-id-xxx');
```
Manual create instance
```bash
use Mesalution\Sms\Sms;

$options = [
    'username'=>'{{username}}',
    'password'=>'{{password}}',
    'otcId'=>'{{otcId}}',
    'senderName'=>'{{senderName}}',
];
$sms = new Sms('promotech',$options);

$otp = $sms->requestOTP('0812345678');

$sms->verifyOTP('otp-id-xxx', '123456');
$sms->resendOTP('otp-id-xxx');
```
Inject in controller
```bash
use Mesalution\Sms\Sms;

class ExampleController extends Controller
{
    public function __constructor(protected Sms $sms){}

    public function index()
    {
        $otp = $this->sms->requestOTP('0812345678');
        $this->sms->verifyOTP('otp-id-xxx', '123456');
        $this->sms->resendOTP('otp-id-xxx');
    }
}
```

## How to implement new driver
Driver must be implement interface `Mesalution\Sms\Contract\Sms`
```bash
use Mesalution\Sms\Contracts\Sms;
use Mesalution\Sms\Data\Otp;

class MySmsDriver implements Sms
{
    public function requestOTP(string $mobile): Otp
    {
        // your logic
    }

    public function verifyOTP(string $otpId, string $otpCode): bool
    {
        // your logic
    }

    public function resendOTP(string $otpId): bool
    {
        // your logic
    }
}
```

## Error Handler
this package will throw exception :
- RequestOtpException 
- VerifyOtpException
- ResendOtpException
- AuthException
- BadResponseException
- ClientException
- ConfirmedOtpException
- ConnectionException
- ExpiredOtpException
- ExternalException
- InternalException
- InvalidOtpException
- OtpException
- SmsException

You can use try-catch for handle:
```bash
try {
    $sms->verifyOTP('otp-id', '000000');
} catch (\Mesalution\Sms\Exceptions\VerifyOtpException $e) {
    // จัดการกรณี otp ผิด
}

```

## Testing
```bash
./vendor/bin/phpunit
```
