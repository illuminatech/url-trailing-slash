<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Routing\CompiledRouteCollection as BaseCompiledRouteCollection;

/**
 * CompiledRouteCollection is an enhanced version of {@see \Illuminate\Routing\CompiledRouteCollection}, which allows
 * restoration of trailing slashes definition from cached routes.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 * @see \Illuminate\Foundation\Console\RouteCacheCommand
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.1.5
 */
class CompiledRouteCollection extends BaseCompiledRouteCollection
{
    /**
     * {@inheritdoc}
     */
    protected function newRoute(array $attributes)
    {
        $route = parent::newRoute($attributes);

        if (array_key_exists('hasTrailingSlash', $attributes)) {
            $route->hasTrailingSlash = $attributes['hasTrailingSlash'];
        }

        return $route;
    }
}
