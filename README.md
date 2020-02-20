# La Poste Suivi API

Implementation of the tracking API version 2 from La Poste.  
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
Subscribe to a new Tracking API to get one, [here](https://developer.laposte.fr/products/suivi/2), then you can instantiate the app :

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

## License

The package is licensed under the MIT license. See [License File](https://github.com/debuss/lapostesuivi/blob/master/LICENSE.md) for more information.
