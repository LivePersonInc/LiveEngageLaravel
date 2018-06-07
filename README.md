# Live Engage Laravel

[![Build Status](https://travis-ci.org/liveperson/live-engage-laravel.svg?branch=master)](https://travis-ci.org/LivePersonNY/LiveEngageLaravel)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/liveperson/live-engage-laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LivePersonNY/LiveEngageLaravel/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/liveperson/live-engage-laravel/badge.svg?branch=master)](https://coveralls.io/github/LivePersonNY/LiveEngageLaravel?branch=master)

[![Packagist](https://img.shields.io/packagist/v/liveperson/live-engage-laravel.svg)](https://packagist.org/packages/LivePersonNY/LiveEngageLaravel)
[![Packagist](https://poser.pugx.org/liveperson/live-engage-laravel/d/total.svg)](https://packagist.org/packages/LivePersonNY/LiveEngageLaravel)
[![Packagist](https://img.shields.io/packagist/l/liveperson/live-engage-laravel.svg)](https://packagist.org/packages/LivePersonNY/LiveEngageLaravel)

Package description: Laravel package to easily tap the LiveEngage developer APIs for such content as Engagement History, Engagement Attributes, and more...

*Use at your own risk. This package carries no SLA or support and is still currently under development.*

## Installation

Install via composer
```bash
composer require LivePersonNY/LiveEngageLaravel
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
LivePersonNY\LiveEngageLaravel\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
'LiveEngage' => LivePersonNY\LiveEngageLaravel\Facades\LiveEngageLaravel::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="LivePersonNY\LiveEngageLaravel\ServiceProvider" --tag="config"
```

## Usage

Create/Obtain an API key from LiveEngage with appropriate permissions for the APIs you intend to access.

Configure your keys/account in `config/services.php`

```php
'liveperson' => [
    'default' => [
	    'key' => 'xxxxxxx',
	    'secret' => 'xxxxxxx',
	    'token' => 'xxxxxxx',
	    'token_secret' => 'xxxxxxx',
	    'account' => '123456',
    ]
],
```

*Example:* Capturing engagement history between 2 date/times using global account configured above.

```php
use LiveEngage;
use Carbon\Carbon;
```
```php
$start = new Carbon('2018-06-01 08:00:00');
$end = new Carbon('2018-06-03 17:00:00');

$history = LiveEngage::history($start, $end)->get();
```

*Example:* Getting engagement history between 2 date/times for specific skill IDs.

```php
use LiveEngage;
use Carbon\Carbon;
```
```php
$start = new Carbon('2018-06-01 08:00:00');
$end = new Carbon('2018-06-03 17:00:00');

$history = LiveEngage::skills([432,676])->history($start, $end)->get();
```

## Security

If you discover any security related issues, please email rlester@liveperson.com
instead of using the issue tracker.

## Credits

- [Robert Lester](https://github.com/LivePersonNY/LiveEngageLaravel)
- [All contributors](https://github.com/LivePersonNY/LiveEngageLaravel/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
