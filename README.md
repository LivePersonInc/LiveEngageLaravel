# Live Engage Laravel

[![Build Status](https://travis-ci.org/live-person-inc/live-engage-laravel.svg?branch=master)](https://travis-ci.org/live-person-inc/live-engage-laravel)
[![styleci](https://styleci.io/repos/LivePersonInc/LiveEngageLaravel/shield)](https://styleci.io/repos/LivePersonInc/LiveEngageLaravel)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/LivePersonInc/LiveEngageLaravel/mini.png)](https://insight.sensiolabs.com/projects/LivePersonInc/LiveEngageLaravel)
[![Coverage Status](https://coveralls.io/repos/github/live-person-inc/live-engage-laravel/badge.svg?branch=master)](https://coveralls.io/github/live-person-inc/live-engage-laravel?branch=master)

[![Packagist](https://img.shields.io/packagist/v/live-person-inc/live-engage-laravel.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)
[![Packagist](https://poser.pugx.org/live-person-inc/live-engage-laravel/d/total.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)
[![Packagist](https://img.shields.io/packagist/l/live-person-inc/live-engage-laravel.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)

Laravel package to easily tap the LiveEngage developer APIs for such content as Engagement History, Engagement Attributes, and more...

**Use at your own risk. This package carries no SLA or support and is still currently under development.**

## Installation

Install via composer
```bash
composer require LivePersonInc/LiveEngageLaravel
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
LivePersonInc\LiveEngageLaravel\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
'LiveEngage' => LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel::class,
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
If you want to have multiple API keys, you can add more arrays for them. The keys for each array are arbitrary, but you will need to specify them later to access specific key sets.
```php
'liveperson' => [
    'default' => [
	    'key' => 'xxxxxxx',
	    'secret' => 'xxxxxxx',
	    'token' => 'xxxxxxx',
	    'token_secret' => 'xxxxxxx',
	    'account' => '123456',
    ],
    'history' => [
	    'key' => 'xxxxxxx',
	    'secret' => 'xxxxxxx',
	    'token' => 'xxxxxxx',
	    'token_secret' => 'xxxxxxx',
	    'account' => '123456',
    ],
    'attributes' => [
	    'key' => 'xxxxxxx',
	    'secret' => 'xxxxxxx',
	    'token' => 'xxxxxxx',
	    'token_secret' => 'xxxxxxx',
	    'account' => '123456',
    ]
],
```
To make an api call on a specific key set...
```php
$history = LiveEngage::key('history')->history();
```
To use the default keyset, you need not use the `key` method at all.
```php
$history = LiveEngage::history()
```


**Example:** Capturing engagement history between 2 date/times using global account configured above.

```php
use LiveEngage;
use Carbon\Carbon;
```
```php
$start = new Carbon('2018-06-01 08:00:00');
$end = new Carbon('2018-06-03 17:00:00');

$history = LiveEngage::history($start, $end);
```

**Example:** Getting engagement history between 2 date/times for specific skill IDs.

```php
use LiveEngage;
use Carbon\Carbon;
```
```php
$start = new Carbon('2018-06-01 08:00:00');
$end = new Carbon('2018-06-03 17:00:00');

$history = LiveEngage::skills([432,676])->history($start, $end);
```
`history()` returns a Laravel collection of Engagement objects.

**Example:** Pulling the next "page" of data in to the collection.

```php
$history->next(); // one page
```
Or
```php
while ($history->next()) {} // get all remaining data
```

**Example:** Iterate through all messages of the transcript

```php
foreach ($history->transcript as $message) {
	echo $message . "\n";
}
```
Transcript is a collection of message objects, so you can access properties of the message as well.
```php
echo $message->time->format('Y-m-d');
```
The time property of the message is a Carbon date object.

## Security

If you discover any security related issues, please email rlester@liveperson.com
instead of using the issue tracker.

## Credits

- [Robert Lester](https://github.com/LivePersonInc/LiveEngageLaravel)
- [All contributors](https://github.com/LivePersonInc/LiveEngageLaravel/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
