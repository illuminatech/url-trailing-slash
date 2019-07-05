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

        if (Str::endsWith($request->getPathInfo(), '/')) {
            if (! $currentRoute->hasTrailingSlash) {
                $url = $request->getSchemeAndHttpHost().rtrim($request->getPathInfo(), '/');
                if (($queryString = $request->getQueryString()) !== null) {
                    $url .= '?'.$queryString;
                }

                return $this->redirect($url);
            }
            return $next($request);
        }

        if ($currentRoute->hasTrailingSlash) {
            $url = $request->getSchemeAndHttpHost().$request->getPathInfo().'/';
            if (($queryString = $request->getQueryString()) !== null) {
                $url .= '?'.$queryString;
            }

            return $this->redirect($url);
        }

        return $next($request);
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
