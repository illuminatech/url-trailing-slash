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


Usage
-----

This extension allows enforcing URL routes with or without trailing slash.

In case URI for particular route is specified with the trailing slash - it will be enforced for this route and request
without slash in the URL end will cause 301 redirection.
In case URI for particular route is specified without the trailing slash - its absence will be enforced for this route
and request containing slash in the URL end will cause 301 redirection.

For example:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::get('items/', ItemController::class.'@index')->name('items.index'); // enforce trailing slash
Route::get('items/{item}', ItemController::class.'@show')->name('items.show'); // enforce no trailing slash
```
