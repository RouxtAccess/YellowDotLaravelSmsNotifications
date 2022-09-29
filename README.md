### YellowDot Notification Channel for Laravel

## SMS Notifications

Send SMS notifications in Laravel powered by [YellowDot](http://yellowdotafrica.com/).

## Version Support
Laravel 9+

## Installation

Install the package :) 

```bash
composer require rouxtaccess/laravel-yellowdot
```

The package will automatically register itself.

You can publish the migration with:

```bash
php artisan vendor:publish --provider="Illuminate\Notifications\SMSPortalServiceProvider"
```

This is the contents of the published config file:

```php
return [
    'client_id' => env('SMS_PORTAL_CLIENT_ID'),
    'secret' => env('SMS_PORTAL_SECRET'),
    'base_uri' => env('SMS_PORTAL_URL', 'https://rest.smsportal.com/v1/'),
    'delivery_enabled' => env('SMS_PORTAL_DELIVERY_ENABLED', true),
];
```

## Formatting SMS Notifications

If a notification supports being sent as an SMS, you should define a `toSmsPortal` method on the notification class. This method will receive a `$notifiable` entity and should return a `Illuminate\Notifications\Messages\SMSPortalMessage` instance:

```php
/**
 * Get the SMSPortal / SMS representation of the notification.
 *
 * @param  mixed  $notifiable
    * @return SMSPortalMessage
    */
public function toSmsPortal($notifiable)
{
    return (new SMSPortalMessage)
                ->content('Your SMS message content');
}
```

## Adding as Delivery Channel
Add the channel `smsportal` to the notification delivery channels.

```php
/**
 * Get the notification's delivery channels.
 *
 * @param  mixed  $notifiable
 * @return array
 */
public function via($notifiable)
{
    return ['mail', 'smsportal'];
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.