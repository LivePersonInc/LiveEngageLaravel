# Live Engage Laravel

[![Build Status](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/badges/build.png?b=master)](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/LivePersonInc/LiveEngageLaravel/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/live-person-inc/live-engage-laravel/v/stable.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)
[![Latest Unstable Version](https://poser.pugx.org/live-person-inc/live-engage-laravel/v/unstable.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)
[![Packagist](https://poser.pugx.org/live-person-inc/live-engage-laravel/d/total.svg)](https://packagist.org/packages/live-person-inc/live-engage-laravel)

Laravel package to easily tap the LiveEngage developer APIs for such content as Engagement History, Engagement Attributes, and more...

**Use at your own risk. This package carries no SLA or support and is still currently under development.**

## Installation

Install via composer
```bash
composer require live-person-inc/live-engage-laravel

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
$history = LiveEngage::key('history')->history(); //messagingHistory() for messaging
```
To use the default keyset, you need not use the `key` method at all.
```php
$history = LiveEngage::history(); //messagingHistory() for messaging
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
while ($next = $history->next()) { $history = history->merge($next) } // get all remaining data
```

**Example:** Iterate through all messages of the transcript

```php
$engagement = $history->find('3498290084'); // This is a collection, so random(), first(), last() all work as well

foreach ($engagement->transcript as $message) {  // For messaging conversations, use messageRecords instead of transcript
	echo $message . "\n";
}
```
Transcript is a collection of message objects, so you can access properties of the message as well.
```php
echo $message->time->format('Y-m-d');
```
The time property of the message is a Carbon date object.

```php
$conversation = LiveEngage::messagingHistory()->first();

foreach ($conversation->transfers as $transfer) {
	echo $transfer->targetSkillName . "\n";
}
```

**Example:** Get messaging agents availability by skill
```php
$availableAgents = LiveEngage::getAgentStatus(['17']);

$online = $availableAgents->state('online');
$away = $availableAgents->state('away');
```

## Security

If you discover any security related issues, please email rlester@liveperson.com
instead of using the issue tracker.

## Credits

- [Robert Lester](https://github.com/LivePersonInc/LiveEngageLaravel)
- [All contributors](https://github.com/LivePersonInc/LiveEngageLaravel/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
