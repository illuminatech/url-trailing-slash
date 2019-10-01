<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;

/**
 * ResourceRegistrar is an enhanced version of {@see \Illuminate\Routing\ResourceRegistrar}, which allows resource routes with trailing slashes definition.
 *
 * This class introduces 2 additional options for the resource routes definition:
 *
 * - 'trailingSlashOnly' - list of resource controller methods, which URL should have a trailing slash
 * - 'trailingSlashExcept' - list of resource controller methods, which URL should not have a trailing slash
 *
 * Example:
 *
 * ```php
 * namespace App\Http\Controllers;
 *
 * use Illuminate\Support\Facades\Route;
 *
 * Route::resource('index-only', ItemController::class, ['trailingSlashOnly' => 'index']);
 * Route::resource('index-plus-show', ItemController::class, ['trailingSlashOnly' => ['index', 'show']]);
 * Route::resource('all-except-show', ItemController::class, ['trailingSlashExcept' => 'show']);
 * Route::resource('all-except-update-and-delete', ItemController::class, ['trailingSlashExcept' => ['update', 'delete']]);
 * ```
 *
 * @see \Illuminatech\UrlTrailingSlash\Router
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class ResourceRegistrar extends BaseResourceRegistrar
{
    /**
     * {@inheritdoc}
     */
    protected function addResourceIndex($name, $base, $controller, $options)
    {
        $route = parent::addResourceIndex($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('index', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceCreate($name, $base, $controller, $options)
    {
        $route = parent::addResourceCreate($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('create', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceStore($name, $base, $controller, $options)
    {
        $route = parent::addResourceStore($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('store', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceShow($name, $base, $controller, $options)
    {
        $route = parent::addResourceShow($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('show', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceEdit($name, $base, $controller, $options)
    {
        $route = parent::addResourceEdit($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('edit', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        $route = parent::addResourceUpdate($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('update', $options);

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        $route = parent::addResourceDestroy($name, $base, $controller, $options);

        $route->hasTrailingSlash = $this->hasTrailingSlash('destroy', $options);

        return $route;
    }

    /**
     * Checks whether specified resource controller method URL should have a trailing slash.
     *
     * @param  string  $method resource controller method name.
     * @param  array  $options route options.
     * @return bool
     */
    protected function hasTrailingSlash($method, $options): bool
    {
        if (! empty($options['trailingSlashExcept'])) {
            if (in_array($method, (array) $options['trailingSlashExcept'], true)) {
                return false;
            }

            if (empty($options['trailingSlashOnly'])) {
                return true;
            }
        }

        if (! empty($options['trailingSlashOnly']) && in_array($method, (array) $options['trailingSlashOnly'], true)) {
            return true;
        }

        return false;
    }
}
