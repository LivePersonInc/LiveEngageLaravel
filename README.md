# Live Engage Laravel

[![Build Status](https://travis-ci.org/liveperson/live-engage-laravel.svg?branch=master)](https://travis-ci.org/liveperson/live-engage-laravel)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/liveperson/live-engage-laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/liveperson/live-engage-laravel/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/liveperson/live-engage-laravel/badge.svg?branch=master)](https://coveralls.io/github/liveperson/live-engage-laravel?branch=master)

[![Packagist](https://img.shields.io/packagist/v/liveperson/live-engage-laravel.svg)](https://packagist.org/packages/liveperson/live-engage-laravel)
[![Packagist](https://poser.pugx.org/liveperson/live-engage-laravel/d/total.svg)](https://packagist.org/packages/liveperson/live-engage-laravel)
[![Packagist](https://img.shields.io/packagist/l/liveperson/live-engage-laravel.svg)](https://packagist.org/packages/liveperson/live-engage-laravel)

Package description: LiveEngage package for laravel

## Installation

Install via composer
```bash
composer require liveperson/live-engage-laravel
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
LivePersonNY\LiveEngageLaravel\Facades\LiveEngageLaravel::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="LivePersonNY\LiveEngageLaravel\ServiceProvider" --tag="config"
```

## Usage

CHANGE ME

## Security

If you discover any security related issues, please email rlester@liveperson.com
instead of using the issue tracker.

## Credits

- [Robert Lester](https://github.com/liveperson/live-engage-laravel)
- [All contributors](https://github.com/liveperson/live-engage-laravel/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
