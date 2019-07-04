<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * RoutingServiceProvider
 *
 * @see \Illuminate\Routing\RoutingServiceProvider::registerUrlGenerator()
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class RoutingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
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

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            'router',
            'url',
        ];
    }
}
