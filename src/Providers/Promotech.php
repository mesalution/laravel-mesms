<?php

namespace Mesalution\LaravelMesms\Providers;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Mesalution\LaravelMesms\Contracts\Sms;
use Mesalution\LaravelMesms\Data\Otp;
use Mesalution\LaravelMesms\Data\Promotech\RequestOtpResponse;
use Mesalution\LaravelMesms\Data\Promotech\VerifyOtpResponse;
use Mesalution\LaravelMesms\Exceptions\ConnectionException;
use Mesalution\LaravelMesms\Exceptions\InternalException;
use Mesalution\LaravelMesms\Exceptions\BadResponseException;
use Mesalution\LaravelMesms\Exceptions\ErrorCode;
use Mesalution\LaravelMesms\Exceptions\ExternalException;
use Mesalution\LaravelMesms\Exceptions\InvalidOtpException;
use Mesalution\LaravelMesms\Exceptions\OtpException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Exceptions\CannotCreateData;
use Throwable;
use UnexpectedValueException;

class Promotech implements Sms
{
    protected ?string $url = null;
    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $otcId = null;
    protected ?string $senderName = null;
    protected PendingRequest $client;

    public function __construct(?array $options = null)
    {
        if (isset($options)) {
            foreach ($options as $key => $value) {
                if (property_exists($this, $key) && is_string($value)) {
                    $this->$key = $value;
                }
            }
        }
        $this->client = Http::baseUrl($this->url)->withBasicAuth($this->username, $this->password)->acceptJson()->asJson();
    }

    protected function post(string $endpoint, array $body = []): array
    {
        try {
            $res = $this->client->post($endpoint, $body);
            $rawData = $res->json();
            return $rawData;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $rawMessage = $e->getMessage();
            if (str_contains($rawMessage, "Connection timed out")) {
                $code = ErrorCode::PROMOTECH_CONNECTION_TIMEOUT;
            } else {
                $code = ErrorCode::PROMOTECH_CONNECTION_REFUSED;
            }
            throw new ConnectionException(
                $e->getMessage(),
                code: $code,
                provider: class_basename($this),
                context: [
                    'url' => $this->url,
                    'endpoint' => $endpoint,
                    'body' => $body,
                ],
                previous: $e,
            );
        } catch (\Throwable $th) {
            throw new InternalException(
                $th->getMessage(),
                provider: class_basename($this),
                code: ErrorCode::PROMOTECH_INTERNAL_ERROR,
                context: [
                    'url' => $this->url,
                    'endpoint' => $endpoint ?? null,
                    'body' => $body ?? [],
                ],
                previous: $th,
            );
        }
    }

    protected function checkSuccessAndErrorField(Data $data): void
    {
        if (!isset($data->success) && !isset($data->error)) {
            throw new UnexpectedValueException("Missing 'success' or 'error' field", ErrorCode::PROMOTECH_MISSING_SUCCESS_OR_ERROR_FIELD->value);
        }
    }
    protected function createBadResponseException(Throwable $e, string $endpoint, array $body, array $rawData): BadResponseException
    {
        $exception = new BadResponseException(
            $e->getMessage(),
            code: ErrorCode::PROMOTECH_BAD_RESPONSE,
            provider: class_basename($this),
            context: [
                'url' => $this->url,
                'endpoint' => $endpoint,
                'body' => $body,
                'rawData' => $rawData,
            ],
            previous: $e,
        );
        return $exception;
    }

    public function requestOTP(string $mobileNo): Otp
    {
        $endpoint = '/otp/requestOTP';
        $body = [
            'otcId' => $this->otcId,
            'mobile' => $mobileNo,
        ];
        $rawData = $this->post($endpoint, $body);
        try {
            $data = RequestOtpResponse::from($rawData);
            $this->checkSuccessAndErrorField($data);
            $otp = new Otp($data->otpId, $data->referrenceCode);
            return $otp;
        } catch (CannotCreateData $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (UnexpectedValueException $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (\Throwable $th) {
            throw new InternalException(
                $th->getMessage(),
                provider: class_basename($this),
                code: ErrorCode::PROMOTECH_INTERNAL_ERROR,
                context: [
                    'url' => $this->url,
                    'endpoint' => $endpoint ?? null,
                    'body' => $body ?? [],
                ],
                previous: $th,
            );
        }
    }

    public function resendOTP(string $otpId): Otp
    {
        $endpoint = '/otp/resendOTP';
        $body = [
            'otpId' => $otpId,
        ];
        $rawData = $this->post($endpoint, $body);
        try {
            $data = RequestOtpResponse::from($rawData);
            $this->checkSuccessAndErrorField($data);
            $otp = new Otp($data->otpId, $data->referrenceCode);
            return $otp;
        } catch (CannotCreateData $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (UnexpectedValueException $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (\Throwable $th) {
            throw new InternalException(
                $th->getMessage(),
                provider: class_basename($this),
                code: ErrorCode::PROMOTECH_INTERNAL_ERROR,
                context: [
                    'url' => $this->url,
                    'endpoint' => $endpoint ?? null,
                    'body' => $body ?? [],
                ],
                previous: $th,
            );
        }
    }

    public function verifyOTP(string $otpId, string $otpCode): void
    {
        $endpoint = '/otp/verifyOTP';
        $body = [
            'otpId' => $otpId,
            'otpCode' => $otpCode,
        ];
        $rawData = $this->post($endpoint, $body);
        try {
            $data = VerifyOtpResponse::from($rawData);
            $this->checkSuccessAndErrorField($data);
            if ($data->result === false) {
                if ($data->isErrorCount === true) {
                    throw new InvalidOtpException(
                        code: ErrorCode::PROMOTECH_INVALID_OTP_CODE,
                        provider: class_basename($this),
                        context: [
                            'otpId' => $otpId,
                            'otpCode' => $otpCode,
                            'verifyData' => $data->toArray(),
                        ]
                    );
                } else if ($data->isExprCode === true) {
                    throw new OtpException(
                        code: ErrorCode::PROMOTECH_OTP_EXPIRED,
                        provider: class_basename($this),
                        context: [
                            'otpId' => $otpId,
                            'otpCode' => $otpCode,
                            'verifyData' => $data->toArray(),
                        ]
                    );
                }
            }
        } catch (CannotCreateData $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (UnexpectedValueException $e) {
            throw $this->createBadResponseException($e, $endpoint, $body, $rawData);
        } catch (OtpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            throw new InternalException(
                $th->getMessage(),
                provider: class_basename($this),
                code: ErrorCode::PROMOTECH_INTERNAL_ERROR,
                context: [
                    'url' => $this->url,
                    'endpoint' => $endpoint ?? null,
                    'body' => $body ?? [],
                ],
                previous: $th,
            );
        }
    }
}
