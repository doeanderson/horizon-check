# Horizon Check

Scheduled health check for Laravel Horizon. Runs `horizon:status` and, when
Horizon is inactive or paused, reports a `HorizonNotRunningException` through
the application's exception handler so your error tracker raises it.

## Installation

Add the repository to the application's `composer.json`, then require the
package:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/doeanderson/horizon-check"
    }
]
```

```sh
composer require doeanderson/horizon-check
```

The service provider is auto-discovered. It registers the `horizon:check`
command and schedules it every thirty minutes.

## Usage

```sh
php artisan horizon:check
```

Exits `0` when Horizon is running and `1` otherwise. The scheduler must be
running for the automatic check to fire, so pair it with an external uptime
monitor if the scheduler and Horizon share a host.

## Configuration

Publish the config to change the schedule:

```sh
php artisan vendor:publish --tag=horizon-check-config
```

`schedule` accepts any cron expression, or `null` to register the command
without scheduling it. It can also be set with the `HORIZON_CHECK_SCHEDULE`
environment variable.

## Testing

```sh
composer test
```
