<?php

namespace RouxtAccess\YellowDotNotifications\Channels;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notification;
use RouxtAccess\YellowDotNotifications\Messages\YellowDotMessage;
use RouxtAccess\YellowDotNotifications\Services\YellowDotNotificationService;
use SayThanks\Electronicline\Exceptions\YellowDotInvalidRouteException;

class YellowDotChannel
{

    protected bool $deliveryEnabled;
    protected bool $deliveryReportsEnabled;
    protected YellowDotNotificationService $notificationService;

    /**
     * Create a new SMSPortal channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->deliveryEnabled = config('yellowdot.sms.delivery_enabled', false);
        $this->deliveryReportsEnabled = config('yellowdot.sms.delivery_reports.enabled', false);
        $this->notificationService = resolve(YellowDotNotificationService::class,
            [
                'password' => config('yellowdot.api.password'),
                'host' => config('yellowdot.api.host'),
            ]
        );
    }


    public function send(mixed $notifiable, Notification $notification): void
    {
        if ($this->deliveryEnabled !== true) {
            return;
        }

        if (!$data = $notifiable->routeNotificationFor('yellowdot', $notification)) {
            throw new YellowDotInvalidRouteException(get_class($notifiable). ' is missing routing function. Please add a `routeNotificationForYellowDot` to the notifiable class');
        }

        if(!method_exists($notification, 'toYellowDot'))
        {
            throw new YellowDotInvalidRouteException('Notification is missing a `toYellowDot` function');
        }
        $message = $notification->toYellowDot($notifiable);

        if (is_string($message)) {
            $message = new YellowDotMessage($message);
        }

        $transactionId = Str::uuid();
        $response = $this->notificationService->send(
            msisdn: $data['to'],
            serviceId: $data['service_id'],
            message: $message->getContent(),
            transactionId: $transactionId,
        );

        if($this->deliveryReportsEnabled){
            // ToDo - Store token to match with delivery report and $transactionId
        }
    }
}
