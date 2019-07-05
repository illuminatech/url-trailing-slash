<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Support\Str;
use Illuminate\Routing\Router as BaseRouter;

/**
 * Router is an enhanced version of {@see \Illuminate\Routing\Router}, which allows routes with trailing slashes definition.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
     */
    protected function prefix($uri): string
    {
        $result = parent::prefix($uri);

        if (Str::endsWith($uri, '/')) {
            return $result.'/';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }
}
