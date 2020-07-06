<p align="center"><img src="https://www.dropbox.com/s/w571kzdajy2ydas/lapostesuivi.jpg?raw=1" alt="Logo" height="120" /></p>
<h1 align="center">La Poste Suivi API</h1>
<p align="center">The best way to track your La Poste, Colissimo and Chronopost packages.</p>
<p align="center">
<a href="//packagist.org/packages/debuss-a/lapostesuivi" rel="nofollow"><img src="https://poser.pugx.org/debuss-a/lapostesuivi/v" alt="Version" style="max-width:100%;"></a>
<a href="//packagist.org/packages/debuss-a/lapostesuivi" rel="nofollow"><img src="https://poser.pugx.org/debuss-a/lapostesuivi/license" alt="License" style="max-width:100%;"></a>
</p><br/>
<p align="center"><img src="https://www.dropbox.com/s/60uc9xqq0f3mzl4/lapostesuivi_example.png?raw=1" alt="Example" /></p><br/>

## What does it do ?

This framework-agnostic package is an implementation of the tracking API version 2 from La Poste.  
This API allows you to track your shipments in real time. "Suivi v2" allows you to harmonize the delivery status of tracked parcels, Colissimo parcels and Chronopost shipments.

More information on the [developer page](https://developer.laposte.fr/products/suivi/2).

## Installation

It is recommended to use [composer](https://getcomposer.org/) to install the package :

```
$ composer require debuss-a/lapostesuivi:^2 
```

PHP 5.6 or newer is required.

## Usage

First of all you need an X-Okapi-Key to use the API.  
Subscribe to a new Tracking API to get one **(it is free)**, [here](https://developer.laposte.fr/products/suivi/2), then you can instantiate the app :

```php
require_once __DIR__.'/vendor/autoload.php';

$app = new LaPoste\Suivi\App('YOUR_X-OKAPI-KEY_HERE');
```

You need to create an object `Request` for every tracking number :

```php
$request = new \LaPoste\Suivi\Request('6M17554710224');
``` 

You can pass 2 more parameters to define the `lang` and `ip_address` you wish to set up.
By default, `lang` is set to `fr_FR` and `ip_address` to `$_SERVER['REMOTE_ADDR']` (or `123.123.123.123` if `REMOTE_ADDR` is not defined).

To track only 1 parcel, you can use the `LaPoste\Suivi\App::call` method :

```php
require_once __DIR__.'/vendor/autoload.php';

$app = new LaPoste\Suivi\App('YOUR_X-OKAPI-KEY_HERE');
$request = new \LaPoste\Suivi\Request('6M17554710224');
$response = $app->call($request);
```

To track more than 1 parcel, use the `LaPoste\Suivi\App::callMultiple` method :

```php
require_once __DIR__.'/vendor/autoload.php';

$app = new LaPoste\Suivi\App('YOUR_X-OKAPI-KEY_HERE');
$requests = [
    new \LaPoste\Suivi\Request('6M17554710224'),
    new \LaPoste\Suivi\Request('EY604176344FR', LaPoste\Suivi\App::LANG_EN),
    new \LaPoste\Suivi\Request('6M17554710224'),
];
$responses = $app->callMultiple($requests);
```

`LaPoste\Suivi\App::call` and `LaPoste\Suivi\App::callMultiple` return instances of [`LaPoste\Suivi\Response`](https://github.com/debuss/lapostesuivi/blob/master/src/Suivi/Response.php).

<ins>Note:</ins> in the case of `LaPoste\Suivi\App::callMultiple`, this package uses `curl_multi*` functions therefore all tracking numbers are tracked asynchronously.  
This means the tracking of multiple packages is done at the same time instead of one by one, and it is much **MUCH!** faster.

## License

The package is licensed under the MIT license. See [License File](https://github.com/debuss/lapostesuivi/blob/master/LICENSE.md) for more information.
