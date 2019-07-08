<p align="center">
    <a href="https://github.com/illuminatech" target="_blank">
        <img src="https://avatars1.githubusercontent.com/u/47185924" height="100px">
    </a>
    <h1 align="center">Laravel URL Route Trailing Slash</h1>
    <br>
</p>

This extension allows enforcing URL routes with or without trailing slash.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://img.shields.io/packagist/v/illuminatech/url-trailing-slash.svg)](https://packagist.org/packages/illuminatech/url-trailing-slash)
[![Total Downloads](https://img.shields.io/packagist/dt/illuminatech/url-trailing-slash.svg)](https://packagist.org/packages/illuminatech/url-trailing-slash)
[![Build Status](https://travis-ci.org/illuminatech/url-trailing-slash.svg?branch=master)](https://travis-ci.org/illuminatech/url-trailing-slash)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist illuminatech/url-trailing-slash
```

or add

```json
"illuminatech/url-trailing-slash": "*"
```

to the require section of your composer.json.

Once package is installed you should manually register `\Illuminatech\UrlTrailingSlash\RoutingServiceProvider` instance at your
application in the way it comes before kernel instantiation, e.g. at the application bootstrap stage. This can be done
in 'bootstrap/app.php' file of regular Laravel application. For example:

```php
<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
// ...

$app->register(new Illuminatech\UrlTrailingSlash\RoutingServiceProvider($app)); // register trailing slashes routing

return $app;
```

> Note: `\Illuminatech\UrlTrailingSlash\RoutingServiceProvider` can not be registered in normal way or be automatically
  discovered by Laravel, since it alters the router, which is bound to the HTTP kernel instance at constructor level.

In order to setup automatic redirection for the routes with trailing slash add `\Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash`
middleware to your HTTP kernel. For example:

```php
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'web' => [
            \Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash::class, // enable automatic redirection on incorrect URL trailing slashes
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // ...
        ],
    
        'api' => [
            // probably you do not need trailing slash redirection anywhere besides public web routes,
            // thus there is no reason for addition its middleware to other groups like API
            'throttle:60,1',
            // ...
        ],
    ];
    // ...
}
```


Usage
-----

This extension allows enforcing URL routes with or without trailing slash. You can decide per each route, whether its URL
should have a trailing slash or not, simply by addition or removal slash symbol ('/') in particular route definition.

In case URI for particular route is specified with the trailing slash - it will be enforced for this route and request
without slash in the URL ending will cause 301 redirection.
In case URI for particular route is specified without the trailing slash - its absence will be enforced for this route
and request containing slash in the URL end will cause 301 redirection.

For example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::get('items/', ItemController::class.'@index')->name('items.index'); // enforce trailing slash
Route::get('items/{item}', ItemController::class.'@show')->name('items.show'); // enforce no trailing slash

// ...

echo route('items.index'); // outputs: 'http://example.com/items/'
echo route('items.show', [1]); // outputs: 'http://example.com/items/1'
```

> Tip: the best SEO practice is having trailing slash at the URLs, which have nested pages, e.g. "defines a folder", and
  have no trailing slashes at the URLs without nested pages, e.g. "pathname of the file".

In case you have setup `\Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash` middleware, application will automatically
redirect request with incorrect URL according to the routes definition. For the example above: request of `http://example.com/items`
causes redirect to `http://example.com/items/` while request to `http://example.com/items/1/` causes redirect to `http://example.com/items/1`.

**Heads up!** Remember, that with this extension installed, you are controlling requirements of URL trailing slashes presence
or absence in **each** route you define. While normally Laravel strips any trailing slashes from route URI automatically,
this extension gives them meaning. You should carefully examine your routes definitions, ensuring you do not set trailing
slash for the wrong ones.


## Slash in Root URL <span id="slash-in-root-url"></span>

Unfortunally this extension is unable to handle trailing slashes for the project root URL, e.g. for a 'home' page.
In other words `\Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash` middleware is unable to distinguish URL
like `http://examle.com` from `http://examle.com/`. This restriction caused by PHP itself, as `$_SERVER['REQUEST_URI']`
value equals to '/' in both cases.

You'll have to deal with trailing slash for root URL separately at the server settings level.
