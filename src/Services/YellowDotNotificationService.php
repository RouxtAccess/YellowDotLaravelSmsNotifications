<?php

namespace RouxtAccess\YellowDotNotifications\Services;

use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use NeoLikotsi\SMSPortal\RestClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SMSPortalMessage;
use RouxtAccess\YellowDotNotifications\Dto\YellowDotSmsPayloadDto;

class YellowDotNotificationService
{
    public const RESULT_CODE_SUCCESS = 1000;

    public string $host;
    public string $password;

    public function __construct(?string $password = null, ?string $host = null)
    {
        $this->password = $password ?? config('yellowdot.api.password');
        $this->host = $host ?? config('yellowdot.api.host');
    }

    public function send(YellowDotSmsPayloadDto $payloadDto) : object
    {
        $endpoint = 'SendSMS';
        $payload = [
            'MSISDN' => $payloadDto->msisdn,
            'ServiceID' => $payloadDto->serviceId,
            'Text' => $payloadDto->text,
            'TransactionID' => $payloadDto->transactionId,
        ];

        return $this->post($endpoint, $payload);
    }

    public function login(int $serviceId): object
    {
        Log::debug('YellowDotNotificationService - Logging in...', ['service_id' => $serviceId,]);
        $endpoint = 'Login';
        $payload = [
            'ServiceID' => $serviceId,
            'Password' => $this->password,
        ];
        $response = $this->post($endpoint, $payload, false);

        Log::debug('YellowDotService - Logged in!', ['service_id' => $serviceId, 'description' => $response->Description]);
        $this->authToken = $response->TokenID;
        $this->storeAuthToken($serviceId, $response->TokenID, $response->TokenExpiration);
        return $response;
    }

    public function clearAuthTokenCache(array $services = null): void
    {
        if(is_null($services))
        {
            $services = array_values(config('yellowdot.subscriptions'));
        }
        foreach ($services as $service){
            Log::debug('YellowDotNotificationService - Clearing Auth Token Cache', ['service' => $service,]);
            Cache::tags(['YellowDot', 'authToken'])->forget($service);
        }
    }
    public function storeAuthToken(int $serviceId, string $tokenId, string $tokenExpiration): void
    {
        $tokenExpirationDuration = Carbon::parse($tokenExpiration)->diffInSeconds();
        Log::debug('YellowDotNotificationService - Caching Auth Token', ['service' => $serviceId, 'duration' => $tokenExpiration]);
        Cache::tags(['YellowDot', 'authToken'])->put($serviceId, $tokenId, $tokenExpirationDuration);
    }

    public function getAuthToken(int $serviceId,)
    {
        $token = Cache::tags(['YellowDot', 'authToken'])->get($serviceId);
        if(!$token)
        {
            $this->login($serviceId);
            $token = Cache::tags(['YellowDot', 'authToken'])->get($serviceId);
        }
        return $token;
    }
    public function post(string $endpoint, array $payload, bool $requiresAuth = true): object
    {
        if($requiresAuth)
        {
            if(!isset($payload['ServiceID']))
            {
                Log::warning('YellowDotNotificationService - No service ID found, but request needs to be authed', ['endpoint' => $endpoint, 'payload' => $payload]);
            }
            $payload['TokenID'] = $payload['TokenID'] ?? $this->getAuthToken($payload['ServiceID']);
        }

        $response = Http::asJson()
            ->post($this->host.$endpoint, $payload)
            ->object();

        if($response?->ResultCode !== static::RESULT_CODE_SUCCESS)
        {
            Log::warning('YellowDotService - Error with Response', ['endpoint' => $endpoint, 'payload' => $payload, 'response' => $response]);
        }
        return $response;
    }
}
