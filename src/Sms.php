<?php

namespace Mesalution\Sms;

use Throwable;
use Mesalution\Sms\Data\Otp;
use Mesalution\Sms\Exceptions\SmsException;
use Mesalution\Sms\Contracts\Sms as SmsInterface;
use Mesalution\Sms\Exceptions\OtpException;
use Mesalution\Sms\Exceptions\ResendOtpException;
use Mesalution\Sms\Exceptions\VerifyOtpException;
use Mesalution\Sms\Exceptions\RequestOtpException;

class Sms
{
    protected SmsInterface $sms;
    public function __construct(?string $driver = null, ?array $options = null)
    {
        $manager = app()->make(SmsManager::class);
        $this->sms = $manager->driver($driver, $options);
    }
    public static function make(string $driver, array $options): static
    {
        $instance = new static($driver, $options);
        return $instance;
    }
    public static function fake(): static
    {
        $instance = new static('fake');
        return $instance;
    }
    public function requestOTP(string $mobileNo): Otp
    {
        try {
            $otp = $this->sms->requestOTP($mobileNo);
            return $otp;
        } catch (SmsException $e) {
            throw new RequestOtpException(
                $e->getMessage(),
                provider: $e->getProvider(),
                previous: $e,
                context: [
                    'mobileNo' => $mobileNo,
                ]
            );
        } catch (Throwable $th) {
            throw new RequestOtpException(
                $th->getMessage(),
                previous: $th,
                context: [
                    'mobileNo' => $mobileNo,
                ]
            );
        }
    }
    public function resendOTP(string $otpId): Otp
    {
        try {
            $otp = $this->sms->resendOTP($otpId);
            return $otp;
        } catch (SmsException $e) {
            throw new ResendOtpException(
                $e->getMessage(),
                provider: $e->getProvider(),
                previous: $e,
                context: [
                    'otpId' => $otpId,
                ]
            );
        } catch (Throwable $th) {
            throw new ResendOtpException(
                $th->getMessage(),
                previous: $th,
                context: [
                    'otpId' => $otpId,
                ]
            );
        }
    }
    public function verifyOTP(string $otpId, string $otpCode): Otp
    {
        try {
            $this->sms->verifyOTP($otpId, $otpCode);
            $otp = new Otp($otpId, result: true, message: 'OTP verified successfully');
            return $otp;
        } catch (OtpException $e) {
            $otp = new Otp($otpId, result: false, message: $e->userMessage());
            return $otp;
        } catch (SmsException $e) {
            throw new VerifyOtpException(
                $e->getMessage(),
                provider: $e->getProvider(),
                previous: $e,
                context: [
                    'otpId' => $otpId,
                    'otpCode' => $otpCode,
                ]
            );
        } catch (Throwable $th) {
            throw new VerifyOtpException(
                $th->getMessage(),
                previous: $th,
                context: [
                    'otpId' => $otpId,
                    'otpCode' => $otpCode,
                ]
            );
        }
    }
}
