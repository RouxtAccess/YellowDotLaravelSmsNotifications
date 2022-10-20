### YellowDot Notification Channel for Laravel

## SMS Notifications

Send SMS notifications in Laravel powered by [YellowDot](http://yellowdotafrica.com/).

## Version Support
Laravel 9+

## Installation

Install the package :) 

```bash
composer require rouxtaccess/yellowdot-notifications
```

The package will automatically register itself.

You can publish the migration with:

```bash
php artisan vendor:publish --provider="RouxtAccess\YellowDotNotifications\YellowDotServiceProvider"
```

This is the contents of the published config file:

```php
return [
    'api' => [
        'host' => env('YELLOW_DOT_HOST', 'https://api.ydplatform.com/api/'),
        'password' => env('YELLOW_DOT_PASSWORD', ),
    ],
    'sms' => [
        'delivery_enabled' => env('YELLOW_DOT_SMS_DELIVERY_ENABLED', false),
        'delivery_reports' => [
            'enabled' => env('YELLOW_DOT_SMS_DELIVERY_REPORTS_ENABLED', false),
        ],
    ],
];
```

## Update Notifiable Model

If a notification supports being sent as an SMS, you should define a `routeNotificationForYellowDot` method on the notification class. This method will receive a `$notifiable` entity and should return a `RouxtAccess\YellowDotNotifications\YellowDotMessage` instance:
public function routeNotificationForYellowDot($notification)
{
if($this->is_for_me)
{
return [
'to' => $this->gifter->msisdn,
'service_id' => config('yellowdot.subscriptions')[User::SUBSCRIPTION_DAILY],
];
}
return [
'to' => $this->recipient_msisdn,
'service_id' => config('yellowdot.subscriptions')[User::SUBSCRIPTION_DAILY],
];

    }
```php

public function toYellowDot($notifiable)
{
    return (new YellowDotMessage)
                ->content('Your SMS message content');
}
```

## Formatting SMS Notifications

If a notification supports being sent as an SMS, you should define a `toYellowDot` method on the notification class. This method will receive a `$notifiable` entity and should return a `RouxtAccess\YellowDotNotifications\YellowDotMessage` instance:

```php

public function toYellowDot($notifiable)
{
    return (new YellowDotMessage)
                ->content('Your SMS message content');
}
```

## Adding as Delivery Channel
Add the channel `yellow_dot` to the notification delivery channels.

```php
/**
 * Get the notification's delivery channels.
 *
 * @param  mixed  $notifiable
 * @return array
 */
public function via($notifiable)
{
    return ['mail', 'yellow_dot'];
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.