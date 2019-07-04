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
 * Router
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
     */
    protected function prefix($uri)
    {
        $uri = parent::prefix($uri);

        if ($uri !== '/' && Str::endsWith($uri, '/')) {
            return $uri.'/';
        }

        return $uri;
    }
}
