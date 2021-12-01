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
[![Build Status](https://github.com/illuminatech/url-trailing-slash/workflows/build/badge.svg)](https://github.com/illuminatech/url-trailing-slash/actions)


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
            // thus there is no reason for addition its middleware to other groups, like API
            'throttle:60,1',
            // ...
        ],
    ];
    // ...
}
```

**Heads up!** Make sure you do not have any trailing slash redirection mechanism at the server configuration level, which
may conflict with `\Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash`. Remember, that by default Laravel
application is shipped with `.htaccess` file, which contains redirection rule enforcing trailing slash absence in project URLs.
Make sure you adjust or disable it, otherwise your application may end in infinite redirection loop.


Usage
-----

This extension allows enforcing URL routes with or without trailing slash. You can decide per each route, whether its URL
should have a trailing slash or not, simply adding or removing slash symbol ('/') in particular route definition.

In case URL for particular route is specified with the trailing slash - it will be enforced for this route, and request
without slash in the URL ending will cause 301 redirection.
In case URL for particular route is specified without the trailing slash - its absence will be enforced for this route,
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
or absence in **each** route you define. While normally Laravel strips any trailing slashes from route URL automatically,
this extension gives them meaning. You should carefully examine your routes definitions, ensuring you do not set trailing
slash for the wrong ones.


### Slash in Root URL <span id="slash-in-root-url"></span>

Unfortunally this extension is unable to handle trailing slashes for the project root URL, e.g. for a 'home' page.
In other words `\Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash` middleware is unable to distinguish URL
like `http://examle.com` from `http://examle.com/`. This restriction caused by PHP itself, as `$_SERVER['REQUEST_URI']`
value equals to '/' in both cases.

You'll have to deal with trailing slash for root URL separately at the server settings level.


### Resource Routes <span id="resource-routes"></span>

You can define trailing slash presence for resource URLs using the same notation as for regular routes. In case resource
name is specified with trailing slash, all its URLs will have it. For example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::resource('items/', ItemController::class); // enforce trailing slash
Route::resource('categories', CategoryController::class); // enforce no trailing slash

// ...

echo route('items.index'); // outputs: 'http://example.com/items/'
echo route('items.show', [1]); // outputs: 'http://example.com/items/1/'

echo route('categories.index'); // outputs: 'http://example.com/categories'
echo route('categories.show', [1]); // outputs: 'http://example.com/categories/1'
```

You can control trailing slash presence per each resource route using options 'trailingSlashOnly' and 'trailingSlashExcept' options.
These ones behave in similar to regular 'only' and 'except', specifying list of resource controller methods, which should
or should not have a trailing slash in their URL. For example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::resource('items', ItemController::class, ['trailingSlashOnly' => 'index']); // trailing slash will be present only for 'index'
Route::resource('categories', CategoryController::class, ['trailingSlashExcept' => 'show']); // trailing slash will be present for all but 'show'

// ...

echo route('items.index'); // outputs: 'http://example.com/items/'
echo route('items.show', [1]); // outputs: 'http://example.com/items/1'

echo route('categories.index'); // outputs: 'http://example.com/categories/'
echo route('categories.show', [1]); // outputs: 'http://example.com/categories/1'
```

> Note: 'trailingSlashExcept' option takes precedence over 'trailingSlashOnly'.


### Trailing Slash in Pagination <span id="trailing-slash-in-pagination"></span>

Unfortunately, the trailing slash will not automatically appear at pagination URLs.
The problem is that Laravel paginators trim the trailing slashes from the URL path at the constructor level.
Thus even adjustment of `\Illuminate\Pagination\Paginator::currentPathResolver()` can not fix the problem.

In case you need a pagination at the URL endpoint with a trailing slash, you should manually set the path for it, using
`\Illuminate\Pagination\AbstractPaginator::withPath()`. For example:

```php
<?php

use App\Models\Item;
use Illuminate\Support\Facades\URL;

$items = Item::query()
    ->paginate()
    ->withPath(URL::current());
```


### Trailing Slash in Unit Tests <span id="trailing-slash-in-unit-tests"></span>

Since `Illuminatech\UrlTrailingSlash\RoutingServiceProvider` can not be registered as regular data provider, while writing
unit and feature tests you will have to manually register it within test application before test kernel instantiation.
This can be done within your `\Tests\CreatesApplication` trait:

```php
<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminatech\UrlTrailingSlash\RoutingServiceProvider;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->register(new RoutingServiceProvider($app)); // register trailing slashes routing

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
```

However, this in not enough to make tests running correctly, because Laravel automatically strips trailing slashes from requests
URL before staring test HTTP request. Thus you will need to override `\Illuminate\Foundation\Testing\Concerns\MakesHttpRequests::prepareUrlForRequest()`
in the way it respects trailing slashes. This can be achieved using `Illuminatech\UrlTrailingSlash\Testing\AllowsUrlTrailingSlash` trait.
For example:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminatech\UrlTrailingSlash\Testing\AllowsUrlTrailingSlash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use AllowsUrlTrailingSlash;
}
```
