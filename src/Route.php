<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Routing\Route as BaseRoute;
use Illuminate\Support\Str;

/**
 * Route allows definition of the URL routes with trailing slashes.
 *
 * In case URI for particular route is specified with the trailing slash - it will be enforced for this route and request
 * without slash in the URL end will cause 301 redirection.
 * In case URI for particular route is specified without the trailing slash - its absence will be enforced for this route
 * and request containing slash in the URL end will cause 301 redirection.
 *
 * For example:
 *
 * ```php
 * namespace App\Http\Controllers;
 *
 * use Illuminate\Support\Facades\Route;
 *
 * Route::get('items/', ItemController::class.'@index')->name('items.index'); // enforce trailing slash
 * Route::get('items/{item}', ItemController::class.'@show')->name('items.show'); // enforce no trailing slash
 * ```
 *
 * @see \Illuminatech\UrlTrailingSlash\Router
 * @see \Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Route extends BaseRoute
{
    /**
     * @var bool whether route URI ends with slash or not.
     */
    public $hasTrailingSlash = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($methods, $uri, $action)
    {
        $this->hasTrailingSlash = Str::endsWith($uri, '/');
        $uri = trim($uri, '/');

        parent::__construct($methods, $uri, $action);
    }
}
