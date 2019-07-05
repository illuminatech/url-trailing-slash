<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * RoutingServiceProvider overrides DI container bindings for the routing components with the ones supporting trailing slashes.
 *
 * This service provider should be registered within the application before kernel instantiation, e.g. at the application
 * bootstrap stage. This can be done in 'bootstrap/app.php' file of regular Laravel application. For example:
 *
 * ```php
 * <?php
 *
 * $app = new Illuminate\Foundation\Application(
 *     $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
 * );
 *
 * $app->singleton(
 *     Illuminate\Contracts\Http\Kernel::class,
 *     App\Http\Kernel::class
 * );
 * // ...
 *
 * $app->register(new Illuminatech\UrlTrailingSlash\RoutingServiceProvider($app));
 *
 * return $app;
 * ```
 *
 * Registering this provided in normal way will have no effect, since it alters the router, which is bound to the HTTP kernel
 * instance at constructor level.
 *
 * @see \Illuminate\Routing\RoutingServiceProvider
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class RoutingServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton('router', function (Container $app) {
            return new Router($app->make('events'), $app);
        });

        $this->app->extend('url', function (\Illuminate\Routing\UrlGenerator $urlGenerator) {
            $newUrlGenerator = new UrlGenerator(
                $this->app->make('router')->getRoutes(),
                $urlGenerator->getRequest(),
                $this->app->make('config')->get('app.asset_url')
            );

            return $newUrlGenerator;
        });
    }
}
