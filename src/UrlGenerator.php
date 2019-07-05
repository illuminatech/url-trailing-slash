<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Support\Str;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

/**
 * UrlGenerator is an enhanced version of {@see \Illuminate\Routing\UrlGenerator} supporting URL with trailing slash generation.
 *
 * @see \Illuminatech\UrlTrailingSlash\Route
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class UrlGenerator extends BaseUrlGenerator
{
    /**
     * {@inheritdoc}
     */
    public function format($root, $path, $route = null): string
    {
        $url = parent::format($root, $path, $route);

        if ($route === null) {
            if (Str::endsWith($path, '/')) {
                return $url.'/';
            }

            return $url;
        }

        if ($route instanceof Route && $route->hasTrailingSlash) {
            return $url.'/';
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function to($path, $extra = [], $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $url = parent::to($path, $extra, $secure);

        if (Str::endsWith($path, '/')) {
            return $url.'/';
        }

        return $url;
    }
}
