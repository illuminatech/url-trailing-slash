<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminatech\UrlTrailingSlash\Route;
use Illuminate\Contracts\Container\Container;

/**
 * RedirectTrailingSlash is a middleware, which performs redirection in case URI trailing slash does not match the route.
 *
 * This middleware should be assigned to the route group, which should maintain SEO, for example:
 *
 * ```php
 * namespace App\Http;
 *
 * use Illuminate\Foundation\Http\Kernel as HttpKernel;
 *
 * class Kernel extends HttpKernel
 * {
 *     protected $middlewareGroups = [
 *         'web' => [
 *             \Illuminatech\UrlTrailingSlash\Middleware\RedirectTrailingSlash::class,
 *             // ...
 *         ],
 *         // ...
 *     ];
 *     // ...
 * }
 * ```
 *
 * > Tip: there is no point to assign this middleware to the routes, which are not indexed by search engines, like API.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class RedirectTrailingSlash
{
    /**
     * @var \Illuminate\Contracts\Container\Container DI container instance.
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param  Container  $container DI container instance.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle an incoming request, performing redirection in case URI trailing slash does not match the route.
     *
     * @param  \Illuminate\Http\Request  $request HTTP request.
     * @param  \Closure  $next
     * @return mixed response.
     */
    public function handle($request, Closure $next)
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $currentRoute = $request->route();
        if (! $currentRoute instanceof Route) {
            return $next($request);
        }

        $pathInfo = $request->getPathInfo();

        if ($pathInfo === '/') {
            // there is no way to determine whether path info empty or equals to single slash from PHP side:
            // `$_SERVER['REQUEST_URI']` equals to '/' in both cases
            return $next($request);
        }

        if (Str::endsWith($pathInfo, '/')) {
            if (! $currentRoute->hasTrailingSlash) {
                $url = $this->createRedirectUrl($request, rtrim($pathInfo, '/'));

                return $this->redirect($url);
            }
            return $next($request);
        }

        if ($currentRoute->hasTrailingSlash) {
            $url = $this->createRedirectUrl($request, $pathInfo.'/');

            return $this->redirect($url);
        }

        return $next($request);
    }

    /**
     * Creates URL for redirection from given request replacing its path with new value.
     *
     * @param  \Illuminate\Http\Request  $request HTTP request.
     * @param  string  $newPath new request path.
     * @return string generated URL.
     */
    protected function createRedirectUrl($request, $newPath): string
    {
        $url = $request->getSchemeAndHttpHost();
        if (($baseUrl = $request->getBaseUrl()) !== null) {
            $url .= $baseUrl;
        }

        $url .= $newPath;

        if (($queryString = $request->getQueryString()) !== null) {
            $url .= '?'.$queryString;
        }

        return $url;
    }

    /**
     * Permanently redirects browser to the new URL.
     *
     * @param  string  $url URL to be redirected to.
     * @return \Illuminate\Http\RedirectResponse response.
     */
    protected function redirect(string $url)
    {
        /* @var $redirector \Illuminate\Routing\Redirector */
        $redirector = $this->container->make('redirect');

        return $redirector->to($url, 301);
    }
}
