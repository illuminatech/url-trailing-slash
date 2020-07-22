<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Routing\RouteCollection as BaseRouteCollection;

/**
 * RouteCollection is an enhanced version of {@see \Illuminate\Routing\RouteCollection}, which allows saving trailing
 * slashes definition while caching routes.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 * @see \Illuminate\Foundation\Console\RouteCacheCommand
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.1.5
 */
class RouteCollection extends BaseRouteCollection
{
    /**
     * {@inheritdoc}
     */
    public function compile()
    {
        $compiled = parent::compile();

        foreach ($this->getRoutes() as $route) {
            if (! $route instanceof Route) {
                continue;
            }

            $compiled['attributes'][$route->getName()]['hasTrailingSlash'] = $route->hasTrailingSlash;
        }

        return $compiled;
    }
}
